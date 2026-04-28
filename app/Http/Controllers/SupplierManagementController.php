<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierManagementController extends Controller
{
    public function index(Request $request)
    {
        $openPoSubquery = DB::table('purchase_orders')
            ->selectRaw('supplier_id, COUNT(*) as open_purchase_orders_count')
            ->whereIn('status', ['draft', 'sent', 'confirmed', 'partial', 'delayed'])
            ->groupBy('supplier_id');

        $suppliersQuery = Supplier::query()
            ->withCount(['productCategories as products_count'])
            ->leftJoinSub($openPoSubquery, 'open_po_stats', function ($join) {
                $join->on('open_po_stats.supplier_id', '=', 'suppliers.id');
            })
            ->select([
                'suppliers.*',
                DB::raw('COALESCE(open_po_stats.open_purchase_orders_count, 0) as open_purchase_orders_count'),
            ]);

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $suppliersQuery->where(function ($builder) use ($search) {
                $builder->where('suppliers.name', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.email', 'like', '%' . $search . '%')
                    ->orWhere('suppliers.phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $suppliersQuery->where('suppliers.status', $request->status);
        }

        $suppliers = $suppliersQuery
            ->orderBy('suppliers.name')
            ->paginate(12)
            ->withQueryString();

        $selectedSupplier = null;
        $selectedSupplierId = $request->integer('supplier');

        if ($selectedSupplierId > 0) {
            $selectedSupplier = $suppliers->getCollection()->firstWhere('id', $selectedSupplierId)
                ?? $this->loadSupplierDetails($selectedSupplierId);
        }

        if (!$selectedSupplier && $suppliers->isNotEmpty()) {
            $selectedSupplier = $this->loadSupplierDetails((int) $suppliers->first()->id);
        }

        $stats = [
            'total' => Supplier::count(),
            'active' => Supplier::where('status', 'active')->count(),
            'review' => Supplier::where('status', 'review')->count(),
            'linked_products' => DB::table('category_suppliers')->count(),
        ];

        $statuses = Supplier::query()
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->pluck('status');

        return view('admin.suppliers.index', compact(
            'suppliers',
            'selectedSupplier',
            'stats',
            'statuses'
        ));
    }

    public function create()
    {
        return view('admin.suppliers.form', $this->formData());
    }

    public function store(Request $request)
    {
        Supplier::create($this->validatedData($request));

        return redirect()->route('admin.purchase-orders.suppliers')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.form', array_merge(
            $this->formData(),
            compact('supplier')
        ));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validatedData($request));

        return redirect()->route('admin.purchase-orders.suppliers')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->productCategories()->exists() || $supplier->purchaseOrders()->exists()) {
            return redirect()->route('admin.purchase-orders.suppliers')
                ->with('error', 'This supplier is linked to product categories or purchase orders and cannot be deleted.');
        }

        $supplier->delete();

        return redirect()->route('admin.purchase-orders.suppliers')
            ->with('success', 'Supplier deleted successfully.');
    }

    protected function formData(): array
    {
        return [
            'statuses' => ['active', 'review'],
        ];
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'status' => 'required|in:active,review',
        ]);
    }

    protected function loadSupplierDetails(int $supplierId): ?Supplier
    {
        $openPoSubquery = DB::table('purchase_orders')
            ->selectRaw('COUNT(*)')
            ->whereColumn('purchase_orders.supplier_id', 'suppliers.id')
            ->whereIn('status', ['draft', 'sent', 'confirmed', 'partial', 'delayed']);

        return Supplier::query()
            ->with(['productCategories:id,name'])
            ->withCount(['productCategories as products_count'])
            ->addSelect([
                'open_purchase_orders_count' => $openPoSubquery,
            ])
            ->whereKey($supplierId)
            ->first();
    }
}
