<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseOrderReceivingService;
use App\Services\PurchaseRoleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    public function __construct(private readonly PurchaseRoleService $purchaseRoles)
    {
    }

    public function index(Request $request)
    {
        $this->authorizePurchasing();

        $query = PurchaseOrder::query()
            ->whereNull('parent_po_id')
            ->with([
                'request',
                'supplier',
                'buyer',
                'items.product.category',
                'request.department',
                'subPurchaseOrders.supplier',
                'subPurchaseOrders.buyer',
                'subPurchaseOrders.items.product.category',
            ]);

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $query->where(function ($builder) use ($search) {
                $builder->where('po_number', 'like', '%' . $search . '%')
                    ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('subPurchaseOrders', fn ($subQuery) => $subQuery->where('po_number', 'like', '%' . $search . '%'))
                    ->orWhereHas('subPurchaseOrders.supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('request', fn ($requestQuery) => $requestQuery->where('request_no', 'like', '%' . $search . '%'))
                    ->orWhereHas('items.product.category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('subPurchaseOrders.items.product.category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where(function ($builder) use ($request) {
                $builder->where('supplier_id', $request->supplier_id)
                    ->orWhereHas('subPurchaseOrders', fn ($subQuery) => $subQuery->where('supplier_id', $request->supplier_id));
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('request', fn ($requestQuery) => $requestQuery->where('department_id', $request->department_id));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->latest('order_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $selectedPurchaseOrder = null;
        $selectedPurchaseOrderId = $request->integer('po');

        if ($selectedPurchaseOrderId > 0) {
            $selectedPurchaseOrder = $purchaseOrders->getCollection()->firstWhere('id', $selectedPurchaseOrderId)
                ?? $this->loadPurchaseOrderDetails($selectedPurchaseOrderId);
        }

        if (!$selectedPurchaseOrder && $purchaseOrders->isNotEmpty()) {
            $selectedPurchaseOrder = $this->loadPurchaseOrderDetails((int) $purchaseOrders->first()->id);
        }

        $stats = [
            'total' => PurchaseOrder::whereNull('parent_po_id')->count(),
            'open' => PurchaseOrder::whereNull('parent_po_id')->whereIn('status', ['draft', 'sent', 'confirmed', 'partial', 'delayed'])->count(),
            'sent' => PurchaseOrder::whereNull('parent_po_id')->where('status', 'sent')->count(),
            'confirmed' => PurchaseOrder::whereNull('parent_po_id')->where('status', 'confirmed')->count(),
            'partial' => PurchaseOrder::whereNull('parent_po_id')->where('status', 'partial')->count(),
        ];

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $departments = $this->safeDepartments();
        $statuses = $this->statuses();

        return view('admin.purchase_orders.index', compact(
            'purchaseOrders',
            'selectedPurchaseOrder',
            'stats',
            'suppliers',
            'departments',
            'statuses'
        ));
    }

    public function create()
    {
        $this->authorizePurchasing();

        return view('admin.purchase_orders.form', $this->formData());
    }

    public function store(Request $request)
    {
        $this->authorizePurchasing();

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
        $this->authorizePurchasing();

        $purchaseOrder->load([
            'parent',
            'request.requester',
            'supplier',
            'buyer',
            'items.product.category',
            'subPurchaseOrders.supplier',
            'subPurchaseOrders.buyer',
            'subPurchaseOrders.items.product.category',
        ]);

        return view('admin.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $this->authorizePurchasing();

        $purchaseOrder->load(['items.product']);

        return view('admin.purchase_orders.form', array_merge(
            $this->formData(),
            compact('purchaseOrder')
        ));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizePurchasing();

        $data = $this->validateData($request, $purchaseOrder->id);

        DB::transaction(function () use ($purchaseOrder, $data) {
            $purchaseOrder->update($data['purchase_order']);
            $purchaseOrder->items()->delete();
            $this->syncItems($purchaseOrder, $data['items']);
        });

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order updated successfully.');
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->authorizePurchasing();

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->statuses()),
        ]);

        $oldStatus = $purchaseOrder->status;
        $newStatus = $validated['status'];

        $purchaseOrder->loadMissing('subPurchaseOrders.supplier');

        if ($purchaseOrder->subPurchaseOrders->isNotEmpty()) {
            foreach ($purchaseOrder->subPurchaseOrders as $subPurchaseOrder) {
                $subOldStatus = $subPurchaseOrder->status;
                $subPurchaseOrder->update(['status' => $newStatus]);
                $this->sendSupplierEmailIfNeeded($subPurchaseOrder, $subOldStatus, $newStatus);
            }
        } else {
            $purchaseOrder->update([
                'status' => $newStatus,
            ]);
            $this->sendSupplierEmailIfNeeded($purchaseOrder, $oldStatus, $newStatus);
        }

        $purchaseOrder->refreshStatusFromSubOrders();

        return redirect()->back()
            ->with('success', 'Purchase order status updated successfully.');
    }

    public function receive(
        Request $request,
        PurchaseOrder $purchaseOrder,
        PurchaseOrderReceivingService $receivingService
    ) {
        $this->authorizePurchasing();

        $validated = $request->validate([
            'receipts' => 'required|array|min:1',
            'receipts.*' => 'required|numeric|min:0',
        ]);

        $updatedPurchaseOrder = $receivingService->receive($purchaseOrder, $validated['receipts']);
        $message = $updatedPurchaseOrder->status === 'completed'
            ? 'All items received. Purchase order marked completed.'
            : 'Received quantities updated. Purchase order marked partial.';

        return redirect()->back()
            ->with('success', $message);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->authorizePurchasing();

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->load('subPurchaseOrders.items');

            foreach ($purchaseOrder->subPurchaseOrders as $subPurchaseOrder) {
                $subPurchaseOrder->items()->delete();
                $subPurchaseOrder->delete();
            }

            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
        });

        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase order deleted successfully.');
    }

    protected function validateData(Request $request, ?int $purchaseOrderId = null): array
    {
        $validated = $request->validate([
            'po_number' => 'nullable|string|max:50|unique:purchase_orders,po_number,' . $purchaseOrderId,
            'request_id' => 'nullable|exists:requests,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'buyer_id' => 'required|exists:users,id',
            'status' => 'required|in:' . implode(',', $this->statuses()),
            'order_date' => 'required|date',
            'expected_delivery' => 'nullable|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
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

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'At least one purchase order item is required.',
            ]);
        }

        return [
            'purchase_order' => [
                'po_number' => $validated['po_number'] ?: $this->generatePoNumber(),
                'request_id' => $validated['request_id'] ?? null,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'buyer_id' => $validated['buyer_id'],
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
        $requestQuery = PurchaseRequest::query()
            ->whereIn('status', ['approved', 'ordered'])
            ->orderByDesc('id');

        return [
            'requests' => $requestQuery->get(['id', 'request_no']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'buyers' => User::orderBy('name')->get(['id', 'name']),
            'products' => Product::orderBy('name')->get(['id', 'name', 'unit']),
            'statuses' => $this->statuses(),
            'defaultPoNumber' => $this->generatePoNumber(),
        ];
    }

    protected function statuses(): array
    {
        return ['draft', 'sent', 'confirmed', 'partial', 'completed', 'delayed'];
    }

    protected function generatePoNumber(): string
    {
        $year = Carbon::now()->format('Y');
        $prefix = 'PO-' . $year . '-';

        $maxNumber = PurchaseOrder::query()
            ->where('po_number', 'like', $prefix . '%')
            ->pluck('po_number')
            ->reduce(function (int $max, string $poNumber) use ($prefix) {
                $pattern = '/^' . preg_quote($prefix, '/') . '(\d+)(?:-[A-Z]+)?$/';

                if (!preg_match($pattern, $poNumber, $matches)) {
                    return $max;
                }

                return max($max, (int) $matches[1]);
            }, 0);

        if ($maxNumber <= 0) {
            return $prefix . '0001';
        }

        return $prefix . str_pad($maxNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    protected function loadPurchaseOrderDetails(int $purchaseOrderId): ?PurchaseOrder
    {
        return PurchaseOrder::query()
            ->with([
                'parent',
                'request.requester',
                'supplier',
                'buyer',
                'items.product.category',
                'subPurchaseOrders.supplier',
                'subPurchaseOrders.buyer',
                'subPurchaseOrders.items.product.category',
            ])
            ->find($purchaseOrderId);
    }

    protected function sendSupplierEmailIfNeeded(PurchaseOrder $purchaseOrder, string $oldStatus, string $newStatus): void
    {
        if ($oldStatus === 'sent' || $newStatus !== 'sent' || !$purchaseOrder->supplier?->email) {
            return;
        }

        try {
            Mail::to($purchaseOrder->supplier->email)
                ->queue(new \App\Mail\PurchaseOrderSupplierMail($purchaseOrder));
        } catch (\Exception $e) {
            Log::error('Failed to send PO email: ' . $e->getMessage());
        }
    }

    protected function safeDepartments()
    {
        return Schema::hasTable('departments')
            ? Department::orderBy('name')->get(['id', 'name'])
            : collect();
    }

    private function authorizePurchasing(): void
    {
        abort_unless($this->purchaseRoles->canManagePurchaseOrders(auth()->user()), 403);
    }
}
