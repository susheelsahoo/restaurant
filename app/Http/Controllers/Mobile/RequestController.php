<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Concerns\BuildsRequestSummaries;
use App\Http\Controllers\Mobile\Concerns\FormatsMobileValues;
use App\Models\Department;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\PurchaseOrderTemplate;
use App\Models\PurchaseRequest;
use App\Models\RequestItem;
use App\Services\PurchaseOrderGenerationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestController extends Controller
{
    use BuildsRequestSummaries;
    use FormatsMobileValues;

    public function index(?string $requestNo = null)
    {
        if ($requestNo !== null) {
            $purchaseRequest = PurchaseRequest::query()
                ->with([
                    'requester:id,name',
                    'department:id,name',
                    'items.product.category:id,name,monthly_budget',
                    'items.supplier:id,name',
                    'purchaseOrders:id,request_id,parent_po_id,po_number',
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

    public function create()
    {
        return view('mobile.quick-add', array_merge(
            $this->quickAddCatalogData(),
            $this->quickAddTemplateData()
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'needed_by' => 'required|date',
            'priority' => 'required|in:low,normal,urgent',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
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
                    'supplier_id' => null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $purchaseRequest;
        });

        $message = 'Purchase request created successfully.';
        $request->session()->flash('success', $message);

        return response()->json([
            'success' => true,
            'message' => $message,
            'redirect_url' => url('/mobile/request-detail'),
            'request' => [
                'id' => $purchaseRequest->id,
                'request_no' => $purchaseRequest->request_no,
            ],
        ]);
    }

    public function updateStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->statuses()),
            'manager_comment' => 'nullable|string|max:2000',
            'needed_by' => 'nullable|date|after:now',
        ], [
            'needed_by.after' => 'Delivery date must be in the future before confirming.',
        ]);

        $managerComment = trim((string) ($validated['manager_comment'] ?? ''));
        $newNeededBy = filled($validated['needed_by'] ?? null)
            ? Carbon::parse($validated['needed_by'])
            : null;

        if ($validated['status'] === 'returned' && $managerComment === '') {
            throw ValidationException::withMessages([
                'manager_comment' => 'Please add a manager comment before sending the request back.',
            ]);
        }

        $validated['manager_comment'] = $managerComment !== '' ? $managerComment : null;

        if ($validated['status'] === 'approved' && $purchaseRequest->needed_by && $purchaseRequest->needed_by->isPast() && !$newNeededBy) {
            return redirect()->back()
                ->with('error', 'Cannot approve requests with needed by dates in the past.');
        }

        $oldStatus = $purchaseRequest->status;

        DB::transaction(function () use ($purchaseRequest, $validated, $oldStatus, $newNeededBy) {
            $updateData = [
                'status' => $validated['status'],
            ];

            if ($newNeededBy) {
                $updateData['needed_by'] = $newNeededBy;
            }

            if (array_key_exists('manager_comment', $validated)) {
                $updateData['manager_comment'] = $validated['manager_comment'];
            }

            $purchaseRequest->update($updateData);

            if ($validated['status'] === 'approved'
                && ($oldStatus !== 'approved' || !$purchaseRequest->purchaseOrders()->whereNull('parent_po_id')->exists())
            ) {
                $this->createPurchaseOrdersFromRequest($purchaseRequest);
            }
        });

        return redirect('/mobile/request-detail')
            ->with('success', 'Request status updated successfully.');
    }

    public function updateDeliveryDate(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'needed_by' => 'required|date|after:now',
        ], [
            'needed_by.after' => 'Delivery date must be in the future before confirming.',
        ]);

        $purchaseRequest->update([
            'needed_by' => Carbon::parse($validated['needed_by']),
        ]);

        return redirect('/mobile/request-detail')
            ->with('success', 'Delivery date updated successfully.');
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
        $purchaseOrder = $purchaseRequest->purchaseOrders->firstWhere('parent_po_id', null)
            ?? $purchaseRequest->purchaseOrders->first();

        return [
            'id' => $purchaseRequest->id,
            'request_no' => $purchaseRequest->request_no,
            'po_number' => $purchaseOrder?->po_number ?: 'Pending PO',
            'requester' => $purchaseRequest->requester?->name ?: 'Unknown requester',
            'department' => $purchaseRequest->department?->name ?: '-',
            'priority' => ucfirst($purchaseRequest->priority),
            'status' => $purchaseRequest->status,
            'status_label' => $statusMeta['label'],
            'status_tone' => $statusMeta['tone'],
            'manager_comment' => $purchaseRequest->manager_comment,
            'admin_comment' => $purchaseRequest->admin_comment,
            'status_action_url' => url('/mobile/request-detail/' . $purchaseRequest->id . '/status'),
            'delivery_update_url' => url('/mobile/request-detail/' . $purchaseRequest->id . '/delivery-date'),
            'needed_by' => $purchaseRequest->needed_by?->format('M d, Y H:i') ?: '-',
            'needed_by_input' => $purchaseRequest->needed_by?->format('Y-m-d\TH:i') ?: '',
            'needed_by_is_past' => (bool) $purchaseRequest->needed_by?->isPast(),
            'min_needed_by_input' => now()->addMinute()->format('Y-m-d\TH:i'),
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

    private function quickAddCatalogData(): array
    {
        $products = Product::query()
            ->with(['category:id,name', 'category.suppliers:id,name'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->map(function (Product $product) {
                $supplier = $product->category?->suppliers->first();

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

    private function statuses(): array
    {
        return ['submitted', 'approved', 'rejected', 'ordered', 'returned'];
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

    private function createPurchaseOrdersFromRequest(PurchaseRequest $purchaseRequest): void
    {
        app(PurchaseOrderGenerationService::class)->createFromRequest($purchaseRequest, auth()->id());
    }
}
