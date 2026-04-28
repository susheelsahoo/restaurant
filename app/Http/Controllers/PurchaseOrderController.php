<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseOrderReceivingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::query()
            ->with(['request', 'supplier', 'buyer', 'items.product.category', 'request.department']);

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $query->where(function ($builder) use ($search) {
                $builder->where('po_number', 'like', '%' . $search . '%')
                    ->orWhereHas('supplier', fn ($supplierQuery) => $supplierQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('request', fn ($requestQuery) => $requestQuery->where('request_no', 'like', '%' . $search . '%'))
                    ->orWhereHas('items.product.category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
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
            'total' => PurchaseOrder::count(),
            'open' => PurchaseOrder::whereIn('status', ['draft', 'sent', 'confirmed', 'partial', 'delayed'])->count(),
            'sent' => PurchaseOrder::where('status', 'sent')->count(),
            'confirmed' => PurchaseOrder::where('status', 'confirmed')->count(),
            'partial' => PurchaseOrder::where('status', 'partial')->count(),
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
        $purchaseOrder->load(['request.requester', 'supplier', 'buyer', 'items.product.category']);

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

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->statuses()),
        ]);

        $oldStatus = $purchaseOrder->status;
        $newStatus = $validated['status'];

        $purchaseOrder->update([
            'status' => $newStatus,
        ]);

        // Send email to supplier when status changes to "sent"
        if ($oldStatus !== 'sent' && $newStatus === 'sent' && $purchaseOrder->supplier && $purchaseOrder->supplier->email) {
            try {
                Mail::to($purchaseOrder->supplier->email)
                    ->queue(new \App\Mail\PurchaseOrderSupplierMail($purchaseOrder));
            } catch (\Exception $e) {
                // Log the error but don't fail the status update
                Log::error('Failed to send PO email: ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', 'Purchase order status updated successfully.');
    }

    public function receive(
        Request $request,
        PurchaseOrder $purchaseOrder,
        PurchaseOrderReceivingService $receivingService
    ) {
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
            'po_number' => 'nullable|string|max:50|unique:purchase_orders,po_number,' . $purchaseOrderId,
            'request_id' => 'nullable|exists:requests,id',
            'supplier_id' => 'required|exists:suppliers,id',
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
                'supplier_id' => $validated['supplier_id'],
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
        return [
            'requests' => PurchaseRequest::orderByDesc('id')->get(['id', 'request_no']),
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

        $lastNumber = PurchaseOrder::query()
            ->where('po_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('po_number');

        if (!$lastNumber || !preg_match('/(\d+)$/', $lastNumber, $matches)) {
            return $prefix . '0001';
        }

        return $prefix . str_pad(((int) $matches[1]) + 1, 4, '0', STR_PAD_LEFT);
    }

    protected function loadPurchaseOrderDetails(int $purchaseOrderId): ?PurchaseOrder
    {
        return PurchaseOrder::query()
            ->with(['request.requester', 'supplier', 'buyer', 'items.product.category'])
            ->find($purchaseOrderId);
    }

    protected function safeDepartments()
    {
        return Schema::hasTable('departments')
            ? Department::orderBy('name')->get(['id', 'name'])
            : collect();
    }
}
