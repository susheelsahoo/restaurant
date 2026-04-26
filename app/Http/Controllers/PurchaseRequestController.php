<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::query()
            ->with(['requester', 'department', 'items.product', 'items.supplier', 'purchaseOrders'])
            ->withCount('items');

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $query->where(function ($builder) use ($search) {
                $builder->where('request_no', 'like', '%' . $search . '%')
                    ->orWhereHas('requester', fn ($requesterQuery) => $requesterQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('department', fn ($departmentQuery) => $departmentQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('items.product', fn ($productQuery) => $productQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('items', fn ($itemsQuery) => $itemsQuery->where('notes', 'like', '%' . $search . '%'));
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('needed_by', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('needed_by', '<=', $request->date_to);
        }

        $purchaseRequests = $query
            ->latest('created_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $selectedPurchaseRequest = null;
        $selectedPurchaseRequestId = $request->integer('selected_request');

        if ($selectedPurchaseRequestId > 0) {
            $selectedPurchaseRequest = $purchaseRequests->getCollection()->firstWhere('id', $selectedPurchaseRequestId)
                ?? $this->loadPurchaseRequestDetails($selectedPurchaseRequestId);
        }

        if (!$selectedPurchaseRequest && $purchaseRequests->isNotEmpty()) {
            $selectedPurchaseRequest = $this->loadPurchaseRequestDetails((int) $purchaseRequests->first()->id);
        }

        $stats = [
            'total' => PurchaseRequest::count(),
            'submitted' => PurchaseRequest::where('status', 'submitted')->count(),
            'approved' => PurchaseRequest::where('status', 'approved')->count(),
            'urgent' => PurchaseRequest::where('priority', 'urgent')->count(),
            'ordered' => PurchaseRequest::where('status', 'ordered')->count(),
        ];

        $departments = $this->safeDepartments();

        return view('admin.purchase_requests.index', [
            'purchaseRequests' => $purchaseRequests,
            'selectedPurchaseRequest' => $selectedPurchaseRequest,
            'stats' => $stats,
            'departments' => $departments,
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
        ]);
    }

    public function create()
    {
        return view('admin.purchase_requests.form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data) {
            $purchaseRequest = PurchaseRequest::create($data['purchase_request']);
            $this->syncItems($purchaseRequest, $data['items']);
        });

        return redirect()->route('admin.purchase-orders.requests')
            ->with('success', 'Purchase request created successfully.');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['requester', 'department', 'items.product', 'items.supplier', 'purchaseOrders']);

        return view('admin.purchase_requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load(['items.product', 'items.supplier']);

        return view('admin.purchase_requests.form', array_merge(
            $this->formData(),
            compact('purchaseRequest')
        ));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $data = $this->validateData($request, $purchaseRequest->id);

        DB::transaction(function () use ($purchaseRequest, $data) {
            $purchaseRequest->update($data['purchase_request']);
            $purchaseRequest->items()->delete();
            $this->syncItems($purchaseRequest, $data['items']);
        });

        return redirect()->route('admin.purchase-orders.requests')
            ->with('success', 'Purchase request updated successfully.');
    }

    public function updateStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->statuses()),
        ]);

        // Prevent approving requests with past needed_by dates
        if ($validated['status'] === 'approved' && $purchaseRequest->needed_by && $purchaseRequest->needed_by->isPast()) {
            return redirect()->back()
                ->with('error', 'Cannot approve requests with needed by dates in the past.');
        }

        DB::transaction(function () use ($purchaseRequest, $validated) {
            $purchaseRequest->update([
                'status' => $validated['status'],
            ]);

            // Create Purchase Orders when request is approved
            if ($validated['status'] === 'approved') {
                $this->createPurchaseOrdersFromRequest($purchaseRequest);
            }
        });

        return redirect()->back()
            ->with('success', 'Request status updated successfully.');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->purchaseOrders()->exists()) {
            return redirect()->route('admin.purchase-orders.requests')
                ->with('error', 'This request is already linked to one or more purchase orders and cannot be deleted.');
        }

        DB::transaction(function () use ($purchaseRequest) {
            $purchaseRequest->items()->delete();
            $purchaseRequest->delete();
        });

        return redirect()->route('admin.purchase-orders.requests')
            ->with('success', 'Purchase request deleted successfully.');
    }

    protected function validateData(Request $request, ?int $purchaseRequestId = null): array
    {
        $validated = $request->validate([
            'request_no' => 'nullable|string|max:50|unique:requests,request_no,' . $purchaseRequestId,
            'user_id' => 'required|exists:users,id',
            'department_id' => 'required|exists:departments,id',
            'priority' => 'required|in:' . implode(',', $this->priorities()),
            'status' => 'required|in:' . implode(',', $this->statuses()),
            'needed_by' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'nullable|numeric|min:0.01',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.notes' => 'nullable|string',
        ]);

        $items = collect($validated['items'] ?? [])
            ->filter(function (array $item) {
                return !empty($item['product_id']) || !empty($item['quantity']) || !empty($item['supplier_id']) || !empty($item['notes']);
            })
            ->map(function (array $item) {
                return [
                    'product_id' => $item['product_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ];
            })
            ->filter(fn (array $item) => !empty($item['product_id']) && (float) $item['quantity'] > 0)
            ->values()
            ->all();

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'At least one valid request item is required.',
            ]);
        }

        return [
            'purchase_request' => [
                'request_no' => $validated['request_no'] ?: $this->generateRequestNumber(),
                'user_id' => $validated['user_id'],
                'department_id' => $validated['department_id'],
                'priority' => $validated['priority'],
                'status' => $validated['status'],
                'needed_by' => $validated['needed_by'],
            ],
            'items' => $items,
        ];
    }

    protected function syncItems(PurchaseRequest $purchaseRequest, array $items): void
    {
        foreach ($items as $item) {
            $purchaseRequest->items()->create($item);
        }
    }

    protected function formData(): array
    {
        return [
            'requesters' => $this->safeUsers(),
            'departments' => $this->safeDepartments(),
            'products' => Schema::hasTable('products') ? Product::orderBy('name')->get(['id', 'name', 'unit', 'estimated_price']) : collect(),
            'suppliers' => Schema::hasTable('suppliers') ? Supplier::orderBy('name')->get(['id', 'name']) : collect(),
            'statuses' => $this->statuses(),
            'priorities' => $this->priorities(),
            'defaultRequestNo' => $this->generateRequestNumber(),
        ];
    }

    protected function statuses(): array
    {
        return ['submitted', 'approved', 'rejected', 'ordered', 'returned'];
    }

    protected function priorities(): array
    {
        return ['low', 'normal', 'urgent'];
    }

    protected function generateRequestNumber(): string
    {
        $year = Carbon::now()->format('Y');
        $prefix = 'REQ-' . $year . '-';

        $lastNumber = PurchaseRequest::query()
            ->where('request_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('request_no');

        if (!$lastNumber || !preg_match('/(\d+)$/', $lastNumber, $matches)) {
            return $prefix . '0001';
        }

        return $prefix . str_pad(((int) $matches[1]) + 1, 4, '0', STR_PAD_LEFT);
    }

    protected function loadPurchaseRequestDetails(int $purchaseRequestId): ?PurchaseRequest
    {
        return PurchaseRequest::query()
            ->with(['requester', 'department', 'items.product', 'items.supplier', 'purchaseOrders'])
            ->withCount('items')
            ->find($purchaseRequestId);
    }

    protected function safeUsers()
    {
        return Schema::hasTable('users')
            ? User::orderBy('name')->get(['id', 'name'])
            : collect();
    }

    protected function safeDepartments()
    {
        return Schema::hasTable('departments')
            ? Department::orderBy('name')->get(['id', 'name'])
            : collect();
    }

    protected function createPurchaseOrdersFromRequest(PurchaseRequest $purchaseRequest): void
    {
        // Load items with relationships
        $purchaseRequest->load(['items.supplier', 'items.product:id,estimated_price']);

        // Group items by supplier
        $itemsBySupplier = $purchaseRequest->items
            ->filter(fn ($item) => $item->supplier_id !== null)
            ->groupBy('supplier_id');

        // Create a PO for each supplier
        foreach ($itemsBySupplier as $supplierId => $items) {
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->generatePoNumber(),
                'request_id' => $purchaseRequest->id,
                'supplier_id' => $supplierId,
                'buyer_id' => auth()->id(),
                'status' => 'draft',
                'order_date' => Carbon::now()->toDateString(),
                'expected_delivery' => $purchaseRequest->needed_by->toDateString(),
            ]);

            // Create PO items from request items
            foreach ($items as $requestItem) {
                $purchaseOrder->items()->create([
                    'product_id' => $requestItem->product_id,
                    'quantity' => $requestItem->quantity,
                    'received_qty' => 0,
                    'unit_price' => (float) ($requestItem->product?->estimated_price ?? 0),
                ]);
            }
        }
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
}
