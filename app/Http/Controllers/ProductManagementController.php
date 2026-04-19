<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductManagementController extends Controller
{
    public function index(Request $request)
    {
        $supplierSubquery = DB::table('product_suppliers')
            ->join('suppliers', 'suppliers.id', '=', 'product_suppliers.supplier_id')
            ->selectRaw('product_suppliers.product_id, MIN(suppliers.name) as preferred_supplier_name')
            ->groupBy('product_suppliers.product_id');

        $requestItemsSubquery = DB::table('request_items')
            ->selectRaw('product_id, COUNT(*) as request_lines_count, COALESCE(SUM(quantity), 0) as requested_quantity')
            ->groupBy('product_id');

        $poItemsSubquery = DB::table('po_items')
            ->selectRaw('product_id, COUNT(*) as po_lines_count, COALESCE(SUM(quantity), 0) as ordered_quantity')
            ->groupBy('product_id');

        $productsQuery = Product::query()
            ->with('category:id,name')
            ->leftJoinSub($supplierSubquery, 'preferred_suppliers', function ($join) {
                $join->on('preferred_suppliers.product_id', '=', 'products.id');
            })
            ->leftJoinSub($requestItemsSubquery, 'request_item_stats', function ($join) {
                $join->on('request_item_stats.product_id', '=', 'products.id');
            })
            ->leftJoinSub($poItemsSubquery, 'po_item_stats', function ($join) {
                $join->on('po_item_stats.product_id', '=', 'products.id');
            })
            ->select([
                'products.*',
                DB::raw('preferred_suppliers.preferred_supplier_name'),
                DB::raw('COALESCE(request_item_stats.request_lines_count, 0) as request_lines_count'),
                DB::raw('COALESCE(request_item_stats.requested_quantity, 0) as requested_quantity'),
                DB::raw('COALESCE(po_item_stats.po_lines_count, 0) as po_lines_count'),
                DB::raw('COALESCE(po_item_stats.ordered_quantity, 0) as ordered_quantity'),
            ]);

        if ($request->filled('q')) {
            $search = trim((string) $request->q);

            $productsQuery->where(function ($builder) use ($search) {
                $builder->where('products.name', 'like', '%' . $search . '%')
                    ->orWhere('products.sku', 'like', '%' . $search . '%')
                    ->orWhere('products.barcode', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $productsQuery->where('products.category_id', $request->category);
        }

        if ($request->filled('status')) {
            $productsQuery->where('products.status', $request->status);
        }

        if ($request->filled('supplier')) {
            $productsQuery->whereExists(function ($builder) use ($request) {
                $builder->selectRaw('1')
                    ->from('product_suppliers')
                    ->whereColumn('product_suppliers.product_id', 'products.id')
                    ->where('product_suppliers.supplier_id', $request->supplier);
            });
        }

        $products = $productsQuery
            ->orderBy('products.name')
            ->paginate(12)
            ->withQueryString();

        $selectedProduct = null;
        $selectedProductId = $request->integer('product');

        if ($selectedProductId > 0) {
            $selectedProduct = $products->getCollection()->firstWhere('id', $selectedProductId)
                ?? $this->loadProductDetails($selectedProductId);
        }

        if (!$selectedProduct && $products->isNotEmpty()) {
            $selectedProduct = $this->loadProductDetails((int) $products->first()->id);
        }

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'barcode_enabled' => Product::whereNotNull('barcode')->where('barcode', '!=', '')->count(),
            'catalog_linked_suppliers' => Schema::hasTable('product_suppliers')
                ? DB::table('product_suppliers')->distinct('supplier_id')->count('supplier_id')
                : 0,
        ];

        $categories = ProductCategory::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses = Product::query()
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->pluck('status');

        return view('admin.purchase_orders.products.index', compact(
            'products',
            'selectedProduct',
            'stats',
            'categories',
            'suppliers',
            'statuses'
        ));
    }

    public function create()
    {
        return view('admin.products.form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data) {
            $product = Product::create($data['product']);
            $product->suppliers()->sync($data['supplier_ids']);
        });

        return redirect()->route('admin.purchase-orders.products')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load('suppliers:id,name');

        return view('admin.products.form', array_merge(
            $this->formData(),
            compact('product')
        ));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validatedData($request, $product->id);

        DB::transaction(function () use ($data, $product) {
            $product->update($data['product']);
            $product->suppliers()->sync($data['supplier_ids']);
        });

        return redirect()->route('admin.purchase-orders.products')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->poItems()->exists()) {
            return redirect()->route('admin.purchase-orders.products')
                ->with('error', 'This product is linked to purchase orders and cannot be deleted.');
        }

        DB::transaction(function () use ($product) {
            $product->suppliers()->detach();
            $product->delete();
        });

        return redirect()->route('admin.purchase-orders.products')
            ->with('success', 'Product deleted successfully.');
    }

    protected function formData(): array
    {
        return [
            'categories' => ProductCategory::orderBy('name')->get(['id', 'name']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'statuses' => ['active', 'inactive'],
        ];
    }

    protected function validatedData(Request $request, ?int $productId = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'sku' => 'required|string|max:50|unique:products,sku,' . $productId,
            'category_id' => 'nullable|exists:product_categories,id',
            'unit' => 'nullable|string|max:20',
            'barcode' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'estimated_price' => 'nullable|numeric|min:0',
            'supplier_ids' => 'nullable|array',
            'supplier_ids.*' => 'exists:suppliers,id',
        ]);

        return [
            'product' => [
                'name' => $validated['name'],
                'sku' => $validated['sku'],
                'category_id' => $validated['category_id'] ?? null,
                'unit' => $validated['unit'] ?? null,
                'barcode' => $validated['barcode'] ?? null,
                'status' => $validated['status'],
                'estimated_price' => $validated['estimated_price'] ?? null,
            ],
            'supplier_ids' => $validated['supplier_ids'] ?? [],
        ];
    }

    protected function loadProductDetails(int $productId): ?Product
    {
        $requestLinesCountSubquery = DB::table('request_items')
            ->selectRaw('COUNT(*)')
            ->whereColumn('request_items.product_id', 'products.id');

        $requestedQuantitySubquery = DB::table('request_items')
            ->selectRaw('COALESCE(SUM(quantity), 0)')
            ->whereColumn('request_items.product_id', 'products.id');

        $poLinesCountSubquery = DB::table('po_items')
            ->selectRaw('COUNT(*)')
            ->whereColumn('po_items.product_id', 'products.id');

        $orderedQuantitySubquery = DB::table('po_items')
            ->selectRaw('COALESCE(SUM(quantity), 0)')
            ->whereColumn('po_items.product_id', 'products.id');

        return Product::query()
            ->with('category:id,name')
            ->with('suppliers:id,name')
            ->withCount('poItems')
            ->withSum('poItems as ordered_quantity_total', 'quantity')
            ->addSelect([
                'request_lines_count' => $requestLinesCountSubquery,
                'requested_quantity' => $requestedQuantitySubquery,
                'po_lines_count' => $poLinesCountSubquery,
                'ordered_quantity' => $orderedQuantitySubquery,
            ])
            ->whereKey($productId)
            ->first();
    }
}
