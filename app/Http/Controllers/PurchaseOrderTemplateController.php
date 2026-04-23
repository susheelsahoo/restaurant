<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\PurchaseOrderTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrderTemplate::query()
            ->with(['department'])
            ->withCount('items');

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhereHas('department', fn ($departmentQuery) => $departmentQuery->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $templates = $query->latest()->paginate(12)->withQueryString();

        $stats = [
            'total' => PurchaseOrderTemplate::count(),
            'active' => PurchaseOrderTemplate::where('status', 'active')->count(),
            'draft' => PurchaseOrderTemplate::where('status', 'draft')->count(),
            'archived' => PurchaseOrderTemplate::where('status', 'archived')->count(),
        ];

        $departments = Department::orderBy('name')->get(['id', 'name']);
        $statuses = $this->statuses();

        return view('admin.purchase_orders.template.index', compact('templates', 'stats', 'departments', 'statuses'));
    }

    public function create()
    {
        return view('admin.purchase_orders.template.form', $this->formData());
    }

    public function store(Request $request)
    {
        $payload = $this->validatedPayload($request);

        DB::transaction(function () use ($payload) {
            $template = PurchaseOrderTemplate::create($payload['template']);
            $template->items()->createMany($payload['items']);
        });

        return redirect()->route('admin.purchase-order-templates.index')
            ->with('success', 'Purchase order template created successfully.');
    }

    public function edit(PurchaseOrderTemplate $purchaseOrderTemplate)
    {
        $purchaseOrderTemplate->load(['items.product', 'department']);

        return view('admin.purchase_orders.template.form', array_merge(
            $this->formData(),
            ['purchaseOrderTemplate' => $purchaseOrderTemplate]
        ));
    }

    public function update(Request $request, PurchaseOrderTemplate $purchaseOrderTemplate)
    {
        $payload = $this->validatedPayload($request);

        DB::transaction(function () use ($purchaseOrderTemplate, $payload) {
            $purchaseOrderTemplate->update($payload['template']);
            $purchaseOrderTemplate->items()->delete();
            $purchaseOrderTemplate->items()->createMany($payload['items']);
        });

        return redirect()->route('admin.purchase-order-templates.index')
            ->with('success', 'Purchase order template updated successfully.');
    }

    public function duplicate(PurchaseOrderTemplate $purchaseOrderTemplate)
    {
        $purchaseOrderTemplate->load('items');

        DB::transaction(function () use ($purchaseOrderTemplate) {
            $duplicate = PurchaseOrderTemplate::create([
                'name' => $this->duplicateName($purchaseOrderTemplate->name),
                'department_id' => $purchaseOrderTemplate->department_id,
                'priority' => $purchaseOrderTemplate->priority,
                'status' => 'draft',
                'description' => $purchaseOrderTemplate->description,
            ]);

            $duplicate->items()->createMany(
                $purchaseOrderTemplate->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'item_name' => $item->item_name,
                        'category_name' => $item->category_name,
                        'default_quantity' => $item->default_quantity,
                        'unit' => $item->unit,
                        'note' => $item->note,
                        'sort_order' => $item->sort_order,
                    ];
                })->all()
            );
        });

        return redirect()->route('admin.purchase-order-templates.index')
            ->with('success', 'Purchase order template duplicated successfully.');
    }

    public function destroy(PurchaseOrderTemplate $purchaseOrderTemplate)
    {
        DB::transaction(function () use ($purchaseOrderTemplate) {
            $purchaseOrderTemplate->items()->delete();
            $purchaseOrderTemplate->delete();
        });

        return redirect()->route('admin.purchase-order-templates.index')
            ->with('success', 'Purchase order template deleted successfully.');
    }

    private function formData(): array
    {
        $supplierSubquery = DB::table('product_suppliers')
            ->join('suppliers', 'suppliers.id', '=', 'product_suppliers.supplier_id')
            ->selectRaw('product_suppliers.product_id, MIN(suppliers.name) as preferred_supplier_name')
            ->groupBy('product_suppliers.product_id');

        $products = Product::query()
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->leftJoinSub($supplierSubquery, 'preferred_suppliers', function ($join) {
                $join->on('preferred_suppliers.product_id', '=', 'products.id');
            })
            ->orderBy('products.name')
            ->get([
                'products.id',
                'products.name',
                'products.unit',
                'products.category_id',
                'product_categories.name as category_name',
                DB::raw('COALESCE(preferred_suppliers.preferred_supplier_name, "") as preferred_supplier_name'),
            ]);

        return [
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'products' => $products,
            'priorities' => $this->priorities(),
            'statuses' => $this->statuses(),
        ];
    }

    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:' . implode(',', $this->priorities()),
            'status' => 'required|in:' . implode(',', $this->statuses()),
            'description' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.default_quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.note' => 'nullable|string|max:1000',
        ]);

        $products = Product::query()
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.category_id')
            ->whereIn('products.id', collect($validated['items'])->pluck('product_id')->filter()->all())
            ->get([
                'products.id',
                'products.name',
                'product_categories.name as category_name',
            ])
            ->keyBy('id');

        $items = collect($validated['items'] ?? [])
            ->map(function (array $item, int $index) use ($products) {
                $product = $products->get((int) $item['product_id']);

                return [
                    'product_id' => (int) $item['product_id'],
                    'item_name' => (string) ($product->name ?? ''),
                    'category_name' => (string) ($product->category_name ?? ''),
                    'default_quantity' => (float) $item['default_quantity'],
                    'unit' => trim((string) ($item['unit'] ?? '')),
                    'note' => trim((string) ($item['note'] ?? '')),
                    'sort_order' => $index,
                ];
            })
            ->filter(function (array $item) {
                return $item['product_id'] > 0 && $item['item_name'] !== '' && $item['unit'] !== '';
            })
            ->values()
            ->all();

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'At least one template item is required.',
            ]);
        }

        return [
            'template' => [
                'name' => $validated['name'],
                'department_id' => $validated['department_id'] ?? null,
                'priority' => $validated['priority'],
                'status' => $validated['status'],
                'description' => $validated['description'] ?? null,
            ],
            'items' => $items,
        ];
    }

    private function priorities(): array
    {
        return ['normal', 'urgent', 'low'];
    }

    private function statuses(): array
    {
        return ['active', 'draft', 'archived'];
    }

    private function duplicateName(string $name): string
    {
        $baseName = trim($name) !== '' ? trim($name) : 'Template';
        $candidate = $baseName . ' Copy';
        $counter = 2;

        while (PurchaseOrderTemplate::where('name', $candidate)->exists()) {
            $candidate = $baseName . ' Copy ' . $counter;
            $counter++;
        }

        return $candidate;
    }
}
