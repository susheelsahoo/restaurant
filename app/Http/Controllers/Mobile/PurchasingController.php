<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mobile\Concerns\FormatsMobileValues;
use App\Mail\PurchaseOrderSupplierMail;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderReceivingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PurchasingController extends Controller
{
    use FormatsMobileValues;

    public function index()
    {
        return view('mobile.orders', [
            'purchaseOrders' => $this->purchaseOrderListData(),
        ]);
    }

    public function show(?PurchaseOrder $purchaseOrder = null)
    {
        if ($purchaseOrder === null) {
            $purchaseOrder = PurchaseOrder::query()
                ->whereNull('parent_po_id')
                ->latest('order_date')
                ->latest('id')
                ->first();
        }

        if (!$purchaseOrder) {
            return redirect('/mobile/orders');
        }

        if ($purchaseOrder->parent_po_id !== null) {
            $purchaseOrder = $purchaseOrder->parent()->firstOrFail();
        }

        return view('mobile.purchase-order', [
            'purchaseOrderReview' => $this->purchaseOrderDetailData($purchaseOrder),
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', $this->purchaseOrderStatuses()),
            'channel' => 'nullable|in:email,whatsapp',
            'only_ready' => 'nullable|boolean',
        ]);

        $newStatus = $validated['status'];
        $channel = $validated['channel'] ?? 'email';
        $onlyReady = $request->boolean('only_ready');

        $purchaseOrder->loadMissing(['supplier', 'subPurchaseOrders.supplier']);
        $purchaseOrdersToUpdate = $purchaseOrder->subPurchaseOrders->isNotEmpty()
            ? $purchaseOrder->subPurchaseOrders
            : collect([$purchaseOrder]);

        if ($newStatus === 'sent' && $onlyReady) {
            $purchaseOrdersToUpdate = $purchaseOrdersToUpdate
                ->filter(fn (PurchaseOrder $order) => $this->isReadyToSend($order))
                ->values();

            if ($purchaseOrdersToUpdate->isEmpty()) {
                throw ValidationException::withMessages([
                    'purchase_order' => 'No ready purchase orders found to send.',
                ]);
            }
        }

        if ($newStatus === 'sent' && $channel === 'email') {
            $missingEmailOrder = $purchaseOrdersToUpdate->first(fn (PurchaseOrder $order) => !$order->supplier?->email);

            if ($missingEmailOrder) {
                throw ValidationException::withMessages([
                    'supplier_email' => 'Supplier email is missing for ' . $missingEmailOrder->po_number . '.',
                ]);
            }
        }

        foreach ($purchaseOrdersToUpdate as $purchaseOrderToUpdate) {
            $oldStatus = $purchaseOrderToUpdate->status;
            $purchaseOrderToUpdate->update(['status' => $newStatus]);
            $this->sendSupplierEmailIfNeeded($purchaseOrderToUpdate, $oldStatus, $newStatus, $channel);
        }

        $purchaseOrder->refreshStatusFromSubOrders();

        $message = $newStatus === 'sent'
            ? ($onlyReady ? 'Ready purchase orders sent successfully.' : 'Purchase order sent successfully.')
            : 'Purchase order status updated successfully.';

        return redirect('/mobile/orders')
            ->with('success', $message);
    }

    public function assignSupplier(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $purchaseOrder->update([
            'supplier_id' => $validated['supplier_id'],
        ]);

        $parentPurchaseOrder = $purchaseOrder->parent_po_id
            ? $purchaseOrder->parent()->first()
            : $purchaseOrder;

        return redirect('/mobile/purchase-order/' . $parentPurchaseOrder->id)
            ->with('success', 'Supplier assigned successfully.');
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

        return redirect('/mobile/orders')
            ->with('success', $message);
    }

    private function purchaseOrderListData(?int $limit = null)
    {
        $query = PurchaseOrder::query()
            ->with([
                'request:id,request_no,department_id',
                'request.department:id,name',
                'supplier:id,name',
                'buyer:id,name',
                'items.product:id,name,category_id,estimated_price',
                'items.product.category:id,name',
                'subPurchaseOrders.supplier:id,name',
                'subPurchaseOrders.items.product:id,name,category_id,estimated_price',
                'subPurchaseOrders.items.product.category:id,name',
            ])
            ->withCount('items')
            ->whereNull('parent_po_id')
            ->latest('order_date')
            ->latest('id');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->map(function (PurchaseOrder $purchaseOrder) {
            $statusMeta = $this->purchaseOrderStatusMeta($purchaseOrder->status);
            $totalAmount = $this->purchaseOrderDisplayTotal($purchaseOrder);

            return [
                'id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'supplier' => $this->purchaseOrderSupplierSummary($purchaseOrder),
                'buyer' => $purchaseOrder->buyer?->name ?: '-',
                'request_no' => $purchaseOrder->request?->request_no ?: '-',
                'category_summary' => $purchaseOrder->category_summary,
                'department' => $purchaseOrder->request?->department?->name ?: '-',
                'status_label' => $statusMeta['label'],
                'status_tone' => $statusMeta['badge_tone'],
                'summary_badge' => $purchaseOrder->status === 'delayed' ? 'badge-yellow' : 'badge-blue',
                'order_date' => $purchaseOrder->order_date?->format('d M Y') ?: '-',
                'expected_delivery' => $purchaseOrder->expected_delivery?->format('d M Y') ?: '-',
                'items_count' => $this->purchaseOrderItemsCount($purchaseOrder),
                'total' => $totalAmount,
                'total_label' => $this->formatMoney($totalAmount),
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
            'items.product:id,name,unit,category_id,estimated_price',
            'items.product.category:id,name',
            'subPurchaseOrders.supplier:id,name,email,phone',
            'subPurchaseOrders.buyer:id,name',
            'subPurchaseOrders.items.product:id,name,unit,category_id,estimated_price',
            'subPurchaseOrders.items.product.category:id,name',
        ]);

        $statusMeta = $this->purchaseOrderStatusMeta($purchaseOrder->status);
        $supplierOrders = $purchaseOrder->subPurchaseOrders->isNotEmpty()
            ? $purchaseOrder->subPurchaseOrders
            : collect([$purchaseOrder]);
        $supplierOrderData = $supplierOrders
            ->map(fn (PurchaseOrder $supplierOrder) => $this->supplierOrderReviewData($supplierOrder))
            ->values();

        $items = $supplierOrderData->flatMap(fn (array $supplierOrder) => $supplierOrder['items'])->values();
        $totalAmount = (float) $supplierOrderData->sum('total');
        $orderedQuantity = (float) $items->sum('ordered_quantity');
        $receivedQuantity = (float) $items->sum('received_quantity');
        $receivedPercent = $orderedQuantity > 0 ? min(100, ($receivedQuantity / $orderedQuantity) * 100) : 0;

        return [
            'id' => $purchaseOrder->id,
            'po_number' => $purchaseOrder->po_number,
            'has_sub_orders' => $purchaseOrder->subPurchaseOrders->isNotEmpty(),
            'supplier_orders' => $supplierOrderData,
            'status' => $purchaseOrder->status,
            'status_label' => $statusMeta['label'],
            'status_pill_tone' => $statusMeta['pill_tone'],
            'supplier' => $this->purchaseOrderSupplierSummary($purchaseOrder),
            'supplier_email' => '-',
            'supplier_phone' => '-',
            'buyer' => $purchaseOrder->buyer?->name ?: '-',
            'request_no' => $purchaseOrder->request?->request_no ?: '-',
            'category_summary' => $purchaseOrder->category_summary,
            'requester' => $purchaseOrder->request?->requester?->name ?: '-',
            'department' => $purchaseOrder->request?->department?->name ?: '-',
            'order_date' => $purchaseOrder->order_date?->format('M d, Y') ?: '-',
            'order_date_short' => $purchaseOrder->order_date?->format('M d') ?: '-',
            'expected_delivery' => $purchaseOrder->expected_delivery?->format('M d, Y') ?: '-',
            'expected_delivery_short' => $purchaseOrder->expected_delivery?->format('M d') ?: '-',
            'items_count' => $items->count(),
            'items' => $items,
            'categories' => collect($supplierOrderData)->flatMap(fn (array $supplierOrder) => $supplierOrder['categories'])->values(),
            'total' => $totalAmount,
            'total_label' => $this->formatMoney($totalAmount),
            'received_label' => $this->formatQuantity($receivedQuantity) . ' / ' . $this->formatQuantity($orderedQuantity),
            'received_percent' => round($receivedPercent),
            'ready_count' => $supplierOrderData->where('dispatch_status', 'ready')->count(),
            'sent_count' => $supplierOrderData->where('dispatch_status', 'sent')->count(),
            'supplier_needed_count' => $supplierOrderData->where('dispatch_status', 'unassigned')->count(),
            'supplier_options' => $this->supplierOptions(),
            'statuses' => $this->purchaseOrderStatuses(),
            'status_actions' => ['confirmed', 'partial', 'completed', 'delayed'],
        ];
    }

    private function supplierOrderReviewData(PurchaseOrder $purchaseOrder): array
    {
        $statusMeta = $this->purchaseOrderStatusMeta($purchaseOrder->status);
        $items = $purchaseOrder->items
            ->map(function ($item) {
                $quantity = (float) $item->quantity;
                $receivedQuantity = (float) $item->received_qty;
                $unitPrice = $this->displayUnitPrice($item);
                $unit = $item->product?->unit ?: 'unit';

                return [
                    'id' => $item->id,
                    'name' => $item->product?->name ?: 'Unknown product',
                    'category' => $item->product?->category?->name ?: 'Uncategorized',
                    'ordered_quantity' => $quantity,
                    'received_quantity' => $receivedQuantity,
                    'unit' => $unit,
                    'ordered_label' => $this->formatQuantity($quantity) . ' ' . $unit,
                    'received_label' => $this->formatQuantity($receivedQuantity) . ' ' . $unit,
                    'unit_price_label' => $this->formatMoney($unitPrice),
                    'unit_price' => $unitPrice,
                    'line_total' => $quantity * $unitPrice,
                    'line_total_label' => $this->formatMoney($quantity * $unitPrice),
                ];
            })
            ->values();
        $totalAmount = (float) $items->sum('line_total');
        $categories = $items
            ->groupBy('category')
            ->map(function ($categoryItems, string $categoryName) {
                return [
                    'name' => $categoryName,
                    'items' => $categoryItems->values(),
                    'items_count' => $categoryItems->count(),
                    'total_label' => $this->formatMoney((float) $categoryItems->sum('line_total')),
                ];
            })
            ->values();
        $orderedQuantity = (float) $items->sum('ordered_quantity');
        $receivedQuantity = (float) $items->sum('received_quantity');
        $receivedPercent = $orderedQuantity > 0 ? min(100, ($receivedQuantity / $orderedQuantity) * 100) : 0;
        $hasSupplier = $purchaseOrder->supplier !== null;
        $sentStatuses = ['sent', 'confirmed', 'partial', 'completed'];
        $dispatchStatus = match (true) {
            !$hasSupplier => 'unassigned',
            $purchaseOrder->status === 'delayed' => 'delayed',
            in_array($purchaseOrder->status, $sentStatuses, true) => 'sent',
            default => 'ready',
        };
        $dispatchMeta = match ($dispatchStatus) {
            'unassigned' => ['label' => 'Supplier Needed', 'tone' => 'orange'],
            'sent' => ['label' => $statusMeta['label'], 'tone' => 'blue'],
            'delayed' => ['label' => 'Delayed', 'tone' => 'red'],
            default => ['label' => 'Ready to Send', 'tone' => 'green'],
        };
        $emailPreview = (new PurchaseOrderSupplierMail($purchaseOrder))->renderedContent();

        return [
            'id' => $purchaseOrder->id,
            'po_number' => $purchaseOrder->po_number,
            'status' => $purchaseOrder->status,
            'status_label' => $statusMeta['label'],
            'status_pill_tone' => $statusMeta['pill_tone'],
            'supplier' => $purchaseOrder->supplier?->name ?: '-',
            'supplier_email' => $purchaseOrder->supplier?->email ?: '-',
            'supplier_phone' => $purchaseOrder->supplier?->phone ?: '-',
            'category_summary' => $purchaseOrder->category_summary,
            'expected_delivery' => $purchaseOrder->expected_delivery?->format('M d, Y') ?: '-',
            'expected_delivery_short' => $purchaseOrder->expected_delivery?->format('M d') ?: '-',
            'items_count' => $items->count(),
            'items' => $items,
            'categories' => $categories,
            'total' => $totalAmount,
            'total_label' => $this->formatMoney($totalAmount),
            'received_label' => $this->formatQuantity($receivedQuantity) . ' / ' . $this->formatQuantity($orderedQuantity),
            'received_percent' => round($receivedPercent),
            'dispatch_status' => $dispatchStatus,
            'dispatch_label' => $dispatchMeta['label'],
            'dispatch_pill_tone' => $dispatchMeta['tone'],
            'status_url' => url('/mobile/purchase-order/' . $purchaseOrder->id . '/status'),
            'assign_supplier_url' => url('/mobile/purchase-order/' . $purchaseOrder->id . '/supplier'),
            'receiving_url' => url('/mobile/purchase-order/' . $purchaseOrder->id . '/receiving'),
            'email_preview_subject' => $emailPreview['subject'],
            'email_preview_html' => $emailPreview['html'],
            'supplier_message_text' => $emailPreview['text'],
        ];
    }

    private function supplierOptions()
    {
        return Supplier::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Supplier $supplier) => [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ])
            ->values();
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

    private function isReadyToSend(PurchaseOrder $purchaseOrder): bool
    {
        return $purchaseOrder->supplier !== null
            && $purchaseOrder->status !== 'delayed'
            && !in_array($purchaseOrder->status, ['sent', 'confirmed', 'partial', 'completed'], true);
    }

    private function purchaseOrderStatuses(): array
    {
        return ['draft', 'sent', 'confirmed', 'partial', 'completed', 'delayed'];
    }

    private function purchaseOrderDisplayTotal(PurchaseOrder $purchaseOrder): float
    {
        return (float) $purchaseOrder->total_amount;
    }

    private function purchaseOrderItemsCount(PurchaseOrder $purchaseOrder): int
    {
        if ($purchaseOrder->subPurchaseOrders->isEmpty()) {
            return (int) ($purchaseOrder->items_count ?? $purchaseOrder->items->count());
        }

        return (int) $purchaseOrder->subPurchaseOrders->sum(fn (PurchaseOrder $subOrder) => $subOrder->items->count());
    }

    private function purchaseOrderSupplierSummary(PurchaseOrder $purchaseOrder): string
    {
        if ($purchaseOrder->subPurchaseOrders->isEmpty()) {
            return $purchaseOrder->supplier?->name ?: '-';
        }

        $suppliers = $purchaseOrder->subPurchaseOrders
            ->pluck('supplier.name')
            ->filter()
            ->unique()
            ->values();

        return $suppliers->isNotEmpty() ? $suppliers->join(', ') : '-';
    }

    private function sendSupplierEmailIfNeeded(PurchaseOrder $purchaseOrder, string $oldStatus, string $newStatus, string $channel): void
    {
        if ($oldStatus === 'sent' || $newStatus !== 'sent' || $channel !== 'email' || !$purchaseOrder->supplier?->email) {
            return;
        }

        try {
            Mail::to($purchaseOrder->supplier->email)
                ->queue(new PurchaseOrderSupplierMail($purchaseOrder));
        } catch (\Exception $e) {
            Log::error('Failed to send mobile PO email: ' . $e->getMessage());
        }
    }

    private function displayUnitPrice($item): float
    {
        $poUnitPrice = (float) $item->unit_price;

        if ($poUnitPrice > 0) {
            return $poUnitPrice;
        }

        return (float) ($item->product?->estimated_price ?? 0);
    }
}
