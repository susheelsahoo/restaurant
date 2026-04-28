<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::query()
            ->with(['suppliers:id,name'])
            ->withCount(['products', 'suppliers'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->q);

                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('monthly_budget', 'like', '%' . $search . '%')
                        ->orWhereHas('suppliers', fn ($supplierQuery) => $supplierQuery->where('name', 'like', '%' . $search . '%'));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.product_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.product_categories.form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data) {
            $productCategory = ProductCategory::create($data['category']);
            $productCategory->suppliers()->sync($data['supplier_ids']);
        });

        return redirect()->route('admin.purchase-orders.product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    public function edit(ProductCategory $productCategory)
    {
        $productCategory->load('suppliers:id,name');

        return view('admin.product_categories.form', array_merge(
            $this->formData(),
            compact('productCategory')
        ));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $data = $this->validatedData($request, $productCategory->id);

        DB::transaction(function () use ($data, $productCategory) {
            $productCategory->update($data['category']);
            $productCategory->suppliers()->sync($data['supplier_ids']);
        });

        return redirect()->route('admin.purchase-orders.product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        if ($productCategory->products()->exists()) {
            return redirect()->route('admin.purchase-orders.product-categories.index')
                ->with('error', 'This category is linked to one or more products and cannot be deleted.');
        }

        DB::transaction(function () use ($productCategory) {
            $productCategory->suppliers()->detach();
            $productCategory->delete();
        });

        return redirect()->route('admin.purchase-orders.product-categories.index')
            ->with('success', 'Product category deleted successfully.');
    }

    protected function formData(): array
    {
        return [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
        ];
    }

    protected function validatedData(Request $request, ?int $productCategoryId = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120|unique:product_categories,slug,' . $productCategoryId,
            'description' => 'nullable|string',
            'monthly_budget' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'supplier_ids' => 'nullable|array',
            'supplier_ids.*' => 'exists:suppliers,id',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        return [
            'category' => [
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'monthly_budget' => $data['monthly_budget'],
                'status' => $data['status'],
            ],
            'supplier_ids' => $data['supplier_ids'] ?? [],
        ];
    }
}
