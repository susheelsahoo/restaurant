<?php

namespace App\Http\Controllers;

use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::query()
            ->with(['request', 'supplier', 'buyer', 'items.product']);

        if ($request->filled('q')) {
            $search = $request->q;

            $query->where(function ($builder) use ($search) {
                $builder->where('po_number', 'like', '%' . $search . '%')
                    ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('buyer', fn ($buyerQuery) => $buyerQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('request', fn ($requestQuery) => $requestQuery->where('request_no', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('buyer_id')) {
            $query->where('buyer_id', $request->buyer_id);
        }

        $purchaseOrders = $query->latest('order_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'open' => PurchaseOrder::whereIn('status', ['draft', 'sent', 'confirmed', 'partial', 'delayed'])->count(),
            'sent' => PurchaseOrder::where('status', 'sent')->count(),
            'confirmed' => PurchaseOrder::where('status', 'confirmed')->count(),
            'partial' => PurchaseOrder::where('status', 'partial')->count(),
        ];

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $buyers = User::orderBy('name')->get(['id', 'name']);
        $statuses = $this->statuses();

        return view('admin.purchase_orders.index', compact('purchaseOrders', 'stats', 'suppliers', 'buyers', 'statuses'));
    }

    public function create()
    {
        return view('admin.purchase_orders.form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data) {
            $purchaseOrder = PurchaseOrder::create($data['purchase_order']);
            $this->syncItems($purchaseOrder, $data['items']);
        });

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['request.requester', 'supplier', 'buyer', 'items.product']);

        return view('admin.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.product']);

        return view('admin.purchase_orders.form', array_merge(
            $this->formData(),
            compact('purchaseOrder')
        ));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $data = $this->validateData($request, $purchaseOrder->id);

        DB::transaction(function () use ($purchaseOrder, $data) {
            $purchaseOrder->update($data['purchase_order']);
            $purchaseOrder->items()->delete();
            $this->syncItems($purchaseOrder, $data['items']);
        });

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order updated successfully.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
        });

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }

    protected function validateData(Request $request, ?int $purchaseOrderId = null): array
    {
        $validated = $request->validate([
            'po_number' => 'required|string|max:50|unique:purchase_orders,po_number,' . $purchaseOrderId,
            'request_id' => 'nullable|exists:requests,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'buyer_id' => 'nullable|exists:users,id',
            'status' => 'required|in:' . implode(',', $this->statuses()),
            'order_date' => 'required|date',
            'expected_delivery' => 'nullable|date|after_or_equal:order_date',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'nullable|numeric|min:0.01',
            'items.*.received_qty' => 'nullable|numeric|min:0',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $items = collect($validated['items'] ?? [])
            ->filter(function (array $item) {
                return !empty($item['product_id']) || !empty($item['quantity']) || !empty($item['unit_price']) || !empty($item['received_qty']);
            })
            ->map(function (array $item) {
                return [
                    'product_id' => $item['product_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'received_qty' => $item['received_qty'] ?? 0,
                    'unit_price' => $item['unit_price'] ?? 0,
                ];
            })
            ->filter(fn (array $item) => !empty($item['product_id']))
            ->values()
            ->all();

        return [
            'purchase_order' => [
                'po_number' => $validated['po_number'],
                'request_id' => $validated['request_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'buyer_id' => $validated['buyer_id'] ?? null,
                'status' => $validated['status'],
                'order_date' => $validated['order_date'],
                'expected_delivery' => $validated['expected_delivery'] ?? null,
            ],
            'items' => $items,
        ];
    }

    protected function syncItems(PurchaseOrder $purchaseOrder, array $items): void
    {
        foreach ($items as $item) {
            $purchaseOrder->items()->create($item);
        }
    }

    protected function formData(): array
    {
        return [
            'requests' => PurchaseRequest::orderByDesc('id')->get(['id', 'request_no']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'buyers' => User::orderBy('name')->get(['id', 'name']),
            'products' => \App\Models\Product::orderBy('name')->get(['id', 'name', 'unit']),
            'statuses' => $this->statuses(),
        ];
    }

    protected function statuses(): array
    {
        return ['draft', 'sent', 'confirmed', 'partial', 'completed', 'delayed'];
    }
}
