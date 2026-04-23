<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\PurchaseRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileController extends Controller
{
    private function requestListData(?int $limit = null)
    {
        $query = PurchaseRequest::query()
            ->with(['department:id,name', 'items.product:id,name'])
            ->withCount('items')
            ->latest('created_at')
            ->latest('id');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->map(function (PurchaseRequest $purchaseRequest) {
            return [
                'request_no' => $purchaseRequest->request_no,
                'department' => optional($purchaseRequest->department)->name ?: '-',
                'status' => $purchaseRequest->status,
                'priority' => ucfirst($purchaseRequest->priority),
                'needed_by' => optional($purchaseRequest->needed_by)->format('d M, H:i') ?: '-',
                'items_count' => $purchaseRequest->items_count,
                'summary' => $purchaseRequest->items
                    ->map(fn ($item) => optional($item->product)->name)
                    ->filter()
                    ->take(3)
                    ->implode(', ') ?: 'No products',
                'is_urgent' => $purchaseRequest->priority === 'urgent',
            ];
        });
    }

    private function commonData(): array
    {
        return [
            'favoriteItems' => ['Tomato', 'Onion', 'Yogurt', 'Flour', 'Oil'],
            'recentItems' => ['Chicken Breast', 'Butter', 'Lemon', 'Cucumber'],
            'basketItems' => [
                ['name' => 'Tomato', 'supplier' => 'FreshFarm', 'category' => 'Vegetables', 'quantity' => '2.5 kg'],
                ['name' => 'Onion', 'supplier' => 'FreshFarm', 'category' => 'Vegetables', 'quantity' => '3 kg'],
                ['name' => 'Yogurt', 'supplier' => 'DairyPlus', 'category' => 'Dairy', 'quantity' => '8 pcs'],
            ],
            'scannedProduct' => [
                'id' => 101,
                'name' => 'Tomato',
                'category' => 'Vegetables',
                'unit' => 'kg',
                'preferred_supplier' => 'FreshFarm',
                'pack_size' => '1 crate',
                'barcode' => '',
            ],
        ];
    }

    private function quickAddCatalogData(): array
    {
        $products = Product::query()
            ->with(['category:id,name', 'suppliers:id,name'])
            ->where('status', 'active')
            ->orderBy('name')
            ->limit(30)
            ->get()
            ->map(function (Product $product) {
                $supplier = $product->suppliers->first();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => optional($product->category)->name ?: 'Uncategorized',
                    'unit' => $product->unit ?: 'pcs',
                    'preferred_supplier' => optional($supplier)->name ?: '-',
                    'supplier_id' => optional($supplier)->id,
                    'barcode' => $product->barcode ?: '',
                ];
            })
            ->values();

        $categories = $products
            ->pluck('category')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return [
            'quickAddProducts' => $products,
            'quickAddCategories' => $categories,
        ];
    }

    public function dashboard()
    {
        $hour = now()->hour;
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }

        $openStatuses = ['submitted', 'approved'];
        $awaitingApprovalStatuses = ['submitted'];

        return view('mobile.dashboard', [
            'stats' => [
                ['value' => PurchaseRequest::count(), 'label' => 'Total Requests'],
                ['value' => PurchaseRequest::where('status', 'approved')->count(), 'label' => 'Approved'],
                ['value' => PurchaseRequest::where('priority', 'urgent')->count(), 'label' => 'Urgent'],
            ],
            'openRequestsCount' => PurchaseRequest::whereIn('status', $openStatuses)->count(),
            'awaitingApprovalCount' => PurchaseRequest::whereIn('status', $awaitingApprovalStatuses)->count(),
            'recentRequests' => $this->requestListData(5),
            'greeting' => $greeting,
        ]);
    }

    public function quickAdd()
    {
        return view('mobile.quick-add', array_merge(
            $this->commonData(),
            $this->quickAddCatalogData()
        ));
    }

    public function storeQuickAdd(Request $request)
    {
        $validated = $request->validate([
            'needed_by' => 'required|date',
            'priority' => 'required|in:low,normal,urgent',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.supplier_id' => 'nullable|exists:suppliers,id',
            'items.*.notes' => 'nullable|string',
        ]);

        $department = Department::query()
            ->where('name', 'Kitchen')
            ->orWhere('name', 'like', '%Kitchen%')
            ->orderBy('name')
            ->first()
            ?? Department::query()->orderBy('name')->first();

        if (!$department) {
            throw ValidationException::withMessages([
                'department_id' => 'Create a department before submitting mobile requests.',
            ]);
        }

        $purchaseRequest = DB::transaction(function () use ($validated, $department) {
            $purchaseRequest = PurchaseRequest::create([
                'request_no' => $this->generateRequestNumber(),
                'user_id' => auth()->id(),
                'department_id' => $department->id,
                'priority' => $validated['priority'],
                'status' => 'submitted',
                'needed_by' => $validated['needed_by'],
                'created_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $purchaseRequest->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $purchaseRequest;
        });

        return response()->json([
            'success' => true,
            'message' => 'Purchase request created successfully.',
            'request' => [
                'id' => $purchaseRequest->id,
                'request_no' => $purchaseRequest->request_no,
            ],
        ]);
    }

    private function generateRequestNumber(): string
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

    public function requestDetail()
    {
        return view('mobile.request-detail', array_merge($this->commonData(), [
            'requests' => $this->requestListData(),
        ]));
    }

    public function approvals()
    {
        return view('mobile.approvals', [
            'approvalRequests' => [
                ['code' => 'REQ-2026-0215', 'requester' => 'Nino G.', 'department' => 'Kitchen', 'summary' => 'Tomato, Onion, Yogurt', 'priority' => 'Urgent', 'neededBy' => 'Today 18:00'],
                ['code' => 'REQ-2026-0214', 'requester' => 'Lasha T.', 'department' => 'Housekeeping', 'summary' => 'Cleaning spray, gloves, paper towels', 'priority' => 'Normal', 'neededBy' => 'Tomorrow morning'],
            ],
        ]);
    }

    public function purchasing() { return view('mobile.purchasing'); }
     public function templates() { return view('mobile.templates'); }
    public function purchaseOrder() { return view('mobile.purchase-order'); }
    public function receiving() { return view('mobile.receiving'); }
}
