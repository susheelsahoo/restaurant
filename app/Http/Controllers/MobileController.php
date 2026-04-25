<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderTemplate;
use App\Models\PurchaseRequest;
use App\Models\RequestItem;
use App\Mail\PurchaseOrderSupplierMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
                'detail_url' => url('/mobile/request-detail/' . $purchaseRequest->request_no),
            ];
        });
    }

    private function requestReviewData(PurchaseRequest $purchaseRequest): array
    {
        $currency = env('PRICE_SIGN', '$');
        $monthlyCategoryTotals = $this->monthlyCategoryTotals();

        $items = $purchaseRequest->items
            ->map(function (RequestItem $item) {
                $product = $item->product;
                $category = $product?->category;
                $quantity = (float) $item->quantity;
                $unitPrice = $product?->estimated_price !== null ? (float) $product->estimated_price : null;

                return [
                    'name' => $product?->name ?: 'Unknown product',
                    'category' => $category?->name ?: 'Uncategorized',
                    'category_budget' => (float) ($category?->monthly_budget ?? 0),
                    'quantity' => $quantity,
                    'quantity_label' => $this->formatQuantity($quantity) . ' ' . ($product?->unit ?: 'unit'),
                    'supplier' => $item->supplier?->name ?: '-',
                    'notes' => $item->notes,
                    'unit_price' => $unitPrice,
                    'line_total' => $unitPrice !== null ? $quantity * $unitPrice : 0,
                    'has_price' => $unitPrice !== null,
                ];
            })
            ->values();

        $categories = $items
            ->groupBy('category')
            ->map(function ($categoryItems, string $categoryName) use ($monthlyCategoryTotals) {
                $monthlyTotal = $monthlyCategoryTotals->get($categoryName, [
                    'cost' => 0,
                    'budget' => (float) $categoryItems->max('category_budget'),
                ]);
                $budget = (float) $monthlyTotal['budget'];
                $requestCost = (float) $categoryItems->sum('line_total');
                $monthlyCost = (float) $monthlyTotal['cost'];
                $usedPct = $budget > 0 ? ($monthlyCost / $budget) * 100 : 0;

                if ($budget <= 0 && $monthlyCost > 0) {
                    $tone = 'warn';
                    $statusText = 'Monthly budget not set';
                    $progressPct = 100;
                } elseif ($usedPct > 100) {
                    $tone = 'over';
                    $statusText = number_format($usedPct, 0) . '% of approved current month spend - over budget';
                    $progressPct = 100;
                } elseif ($usedPct >= 75) {
                    $tone = 'warn';
                    $statusText = number_format($usedPct, 1) . '% of approved current month spend - near limit';
                    $progressPct = $usedPct;
                } else {
                    $tone = 'safe';
                    $statusText = number_format($usedPct, 0) . '% of approved current month spend';
                    $progressPct = $usedPct;
                }

                return [
                    'name' => $categoryName,
                    'items' => $categoryItems->values(),
                    'items_count' => $categoryItems->count(),
                    'request_cost' => $requestCost,
                    'monthly_cost' => $monthlyCost,
                    'budget' => $budget,
                    'used_pct' => $usedPct,
                    'progress_pct' => min(100, max(0, $progressPct)),
                    'tone' => $tone,
                    'status_text' => $statusText,
                ];
            })
            ->values();

        $totalCost = (float) $items->sum('line_total');
        $totalMonthlyCost = (float) $categories->sum('monthly_cost');
        $totalBudget = (float) $categories->sum('budget');
        $warningCategories = $categories
            ->filter(fn (array $category) => $category['tone'] === 'warn')
            ->pluck('name')
            ->values();
        $overBudgetCategories = $categories
            ->filter(fn (array $category) => $category['tone'] === 'over')
            ->pluck('name')
            ->values();
        $statusMeta = $this->requestStatusMeta($purchaseRequest->status);
        $purchaseOrder = $purchaseRequest->purchaseOrders->first();

        return [
            'request_no' => $purchaseRequest->request_no,
            'po_number' => $purchaseOrder?->po_number ?: 'Pending PO',
            'requester' => $purchaseRequest->requester?->name ?: 'Unknown requester',
            'department' => $purchaseRequest->department?->name ?: '-',
            'priority' => ucfirst($purchaseRequest->priority),
            'status' => $purchaseRequest->status,
            'status_label' => $statusMeta['label'],
            'status_tone' => $statusMeta['tone'],
            'needed_by' => $purchaseRequest->needed_by?->format('M d, Y H:i') ?: '-',
            'needed_by_short' => $purchaseRequest->needed_by?->format('M d') ?: '-',
            'created_at' => $purchaseRequest->created_at?->format('M d, Y H:i') ?: '-',
            'items_count' => $items->count(),
            'categories' => $categories,
            'total_cost' => $totalCost,
            'total_monthly_cost' => $totalMonthlyCost,
            'total_budget' => $totalBudget,
            'budget_remaining' => max(0, $totalBudget - $totalMonthlyCost),
            'alert_count' => $warningCategories->count() + $overBudgetCategories->count(),
            'warning_categories' => $warningCategories,
            'over_budget_categories' => $overBudgetCategories,
            'currency' => $currency,
        ];
    }

    private function monthlyCategoryTotals()
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $approvedStatuses = ['approved', 'ordered'];

        return RequestItem::query()
            ->with(['product.category:id,name,monthly_budget'])
            ->whereHas('purchaseRequest', function ($query) use ($approvedStatuses, $monthStart, $monthEnd) {
                $query->whereIn('status', $approvedStatuses)
                    ->whereBetween('created_at', [$monthStart, $monthEnd]);
            })
            ->get()
            ->map(function (RequestItem $item) {
                $product = $item->product;
                $category = $product?->category;
                $quantity = (float) $item->quantity;
                $unitPrice = $product?->estimated_price !== null ? (float) $product->estimated_price : 0;

                return [
                    'category' => $category?->name ?: 'Uncategorized',
                    'budget' => (float) ($category?->monthly_budget ?? 0),
                    'line_total' => $quantity * $unitPrice,
                ];
            })
            ->groupBy('category')
            ->map(fn ($items) => [
                'cost' => (float) $items->sum('line_total'),
                'budget' => (float) $items->max('budget'),
            ]);
    }

    private function requestStatusMeta(string $status): array
    {
        return match ($status) {
            'approved', 'ordered' => ['label' => 'Confirmed', 'tone' => 'green'],
            'rejected' => ['label' => 'Declined', 'tone' => 'red'],
            'returned' => ['label' => 'Sent Back to Requester', 'tone' => 'orange'],
            default => ['label' => 'Pending Manager Approval', 'tone' => 'blue'],
        };
    }

    private function purchaseOrderListData(?int $limit = null)
    {
        $query = PurchaseOrder::query()
            ->with(['request:id,request_no,department_id', 'request.department:id,name', 'supplier:id,name', 'buyer:id,name', 'items'])
            ->withCount('items')
            ->latest('order_date')
            ->latest('id');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->map(function (PurchaseOrder $purchaseOrder) {
            $statusMeta = $this->purchaseOrderStatusMeta($purchaseOrder->status);

            return [
                'id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'supplier' => $purchaseOrder->supplier?->name ?: '-',
                'buyer' => $purchaseOrder->buyer?->name ?: '-',
                'request_no' => $purchaseOrder->request?->request_no ?: '-',
                'department' => $purchaseOrder->request?->department?->name ?: '-',
                'status_label' => $statusMeta['label'],
                'status_tone' => $statusMeta['badge_tone'],
                'summary_badge' => $purchaseOrder->status === 'delayed' ? 'badge-yellow' : 'badge-blue',
                'order_date' => $purchaseOrder->order_date?->format('d M Y') ?: '-',
                'expected_delivery' => $purchaseOrder->expected_delivery?->format('d M Y') ?: '-',
                'items_count' => $purchaseOrder->items_count,
                'total' => (float) $purchaseOrder->total_amount,
                'total_label' => $this->formatMoney((float) $purchaseOrder->total_amount),
                'detail_url' => url('/mobile/purchase-order/' . $purchaseOrder->id),
            ];
        });
    }

    private function purchaseOrderDetailData(PurchaseOrder $purchaseOrder): array
    {
        $purchaseOrder->load([
            'request.requester:id,name',
            'request.department:id,name',
            'supplier:id,name,email,phone',
            'buyer:id,name',
            'items.product:id,name,unit',
        ]);

        $statusMeta = $this->purchaseOrderStatusMeta($purchaseOrder->status);
        $items = $purchaseOrder->items
            ->map(function ($item) {
                $quantity = (float) $item->quantity;
                $receivedQuantity = (float) $item->received_qty;
                $unitPrice = (float) $item->unit_price;
                $unit = $item->product?->unit ?: 'unit';

                return [
                    'name' => $item->product?->name ?: 'Unknown product',
                    'ordered_label' => $this->formatQuantity($quantity) . ' ' . $unit,
                    'received_label' => $this->formatQuantity($receivedQuantity) . ' ' . $unit,
                    'unit_price' => $unitPrice,
                    'line_total' => $quantity * $unitPrice,
                    'line_total_label' => $this->formatMoney($quantity * $unitPrice),
                ];
            })
            ->values();

        $orderedQuantity = (float) $purchaseOrder->items->sum(fn ($item) => (float) $item->quantity);
        $receivedQuantity = (float) $purchaseOrder->items->sum(fn ($item) => (float) $item->received_qty);
        $receivedPercent = $orderedQuantity > 0 ? min(100, ($receivedQuantity / $orderedQuantity) * 100) : 0;

        return [
            'id' => $purchaseOrder->id,
            'po_number' => $purchaseOrder->po_number,
            'status' => $purchaseOrder->status,
            'status_label' => $statusMeta['label'],
            'status_pill_tone' => $statusMeta['pill_tone'],
            'supplier' => $purchaseOrder->supplier?->name ?: '-',
            'supplier_email' => $purchaseOrder->supplier?->email ?: '-',
            'supplier_phone' => $purchaseOrder->supplier?->phone ?: '-',
            'buyer' => $purchaseOrder->buyer?->name ?: '-',
            'request_no' => $purchaseOrder->request?->request_no ?: '-',
            'requester' => $purchaseOrder->request?->requester?->name ?: '-',
            'department' => $purchaseOrder->request?->department?->name ?: '-',
            'order_date' => $purchaseOrder->order_date?->format('M d, Y') ?: '-',
            'order_date_short' => $purchaseOrder->order_date?->format('M d') ?: '-',
            'expected_delivery' => $purchaseOrder->expected_delivery?->format('M d, Y') ?: '-',
            'expected_delivery_short' => $purchaseOrder->expected_delivery?->format('M d') ?: '-',
            'items_count' => $items->count(),
            'items' => $items,
            'total' => (float) $purchaseOrder->total_amount,
            'total_label' => $this->formatMoney((float) $purchaseOrder->total_amount),
            'received_label' => $this->formatQuantity($receivedQuantity) . ' / ' . $this->formatQuantity($orderedQuantity),
            'received_percent' => round($receivedPercent),
            'statuses' => $this->purchaseOrderStatuses(),
        ];
    }

    private function purchaseOrderStatusMeta(string $status): array
    {
        return match ($status) {
            'sent' => ['label' => 'Sent', 'badge_tone' => 'blue', 'pill_tone' => 'blue'],
            'confirmed' => ['label' => 'Confirmed', 'badge_tone' => 'success', 'pill_tone' => 'green'],
            'partial' => ['label' => 'Partial', 'badge_tone' => 'blue', 'pill_tone' => 'orange'],
            'completed' => ['label' => 'Completed', 'badge_tone' => 'success', 'pill_tone' => 'green'],
            'delayed' => ['label' => 'Delayed', 'badge_tone' => 'danger', 'pill_tone' => 'red'],
            default => ['label' => 'Draft', 'badge_tone' => 'secondary', 'pill_tone' => 'blue'],
        };
    }

    private function purchaseOrderStatuses(): array
    {
        return ['draft', 'sent', 'confirmed', 'partial', 'completed', 'delayed'];
    }

    private function formatMoney(float $amount): string
    {
        return config('app.price_sign') . ' ' . number_format($amount, 2);
    }

    private function formatQuantity(float $quantity): string
    {
        return floor($quantity) === $quantity
            ? number_format($quantity, 0)
            : rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');
    }

    private function quickAddCatalogData(): array
    {
        $products = Product::query()
            ->with(['category:id,name', 'suppliers:id,name'])
            ->where('status', 'active')
            ->orderBy('name')
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

        $categories = ProductCategory::query()
            ->orderBy('name')
            ->pluck('name')
            ->filter()
            ->unique()
            ->values();

        return [
            'quickAddProducts' => $products,
            'quickAddCategories' => $categories,
        ];
    }

    private function quickAddTemplateData(): array
    {
        $templates = PurchaseOrderTemplate::query()
            ->with(['items'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(function (PurchaseOrderTemplate $template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'description' => $template->description,
                    'items' => $template->items
                        ->filter(fn ($item) => !empty($item->product_id))
                        ->map(function ($item) {
                            return [
                                'product_id' => (int) $item->product_id,
                                'quantity' => (float) $item->default_quantity,
                                'unit' => $item->unit,
                                'note' => $item->note,
                            ];
                        })
                        ->values(),
                ];
            })
            ->filter(fn (array $template) => $template['items']->isNotEmpty())
            ->values();

        return [
            'quickAddTemplates' => $templates,
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
            $this->quickAddCatalogData(),
            $this->quickAddTemplateData()
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

    public function requestDetail(?string $requestNo = null)
    {
        if ($requestNo !== null) {
            $purchaseRequest = PurchaseRequest::query()
                ->with([
                    'requester:id,name',
                    'department:id,name',
                    'items.product.category:id,name,monthly_budget',
                    'items.supplier:id,name',
                    'purchaseOrders:id,request_id,po_number',
                ])
                ->where('request_no', $requestNo)
                ->firstOrFail();

            return view('mobile.request-show', [
                'requestReview' => $this->requestReviewData($purchaseRequest),
            ]);
        }

        return view('mobile.request-detail', [
            'requests' => $this->requestListData(),
        ]);
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

    public function orders()
    {
        return view('mobile.orders', [
            'purchaseOrders' => $this->purchaseOrderListData(),
        ]);
    }

    public function templates()
    {
        return redirect('/mobile/orders');
    }

    public function purchaseOrder(?PurchaseOrder $purchaseOrder = null)
    {
        if ($purchaseOrder === null) {
            $purchaseOrder = PurchaseOrder::query()
                ->latest('order_date')
                ->latest('id')
                ->first();
        }

        if (!$purchaseOrder) {
            return redirect('/mobile/orders');
        }

        return view('mobile.purchase-order', [
            'purchaseOrderReview' => $this->purchaseOrderDetailData($purchaseOrder),
        ]);
    }

    public function updatePurchaseOrderStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->purchaseOrderStatuses()),
        ]);

        $oldStatus = $purchaseOrder->status;
        $newStatus = $validated['status'];

        $purchaseOrder->update([
            'status' => $newStatus,
        ]);

        if ($oldStatus !== 'sent' && $newStatus === 'sent' && $purchaseOrder->supplier?->email) {
            try {
                Mail::to($purchaseOrder->supplier->email)
                    ->queue(new PurchaseOrderSupplierMail($purchaseOrder));
            } catch (\Exception $e) {
                Log::error('Failed to send mobile PO email: ' . $e->getMessage());
            }
        }

        return redirect('/mobile/purchase-order/' . $purchaseOrder->id)
            ->with('success', 'Purchase order status updated successfully.');
    }

    public function receiving() { return view('mobile.receiving'); }
}
