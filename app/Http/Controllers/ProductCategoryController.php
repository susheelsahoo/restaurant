<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::query()
            ->withCount('products')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->q);

                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('monthly_budget', 'like', '%' . $search . '%');
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
        return view('admin.product_categories.form');
    }

    public function store(Request $request)
    {
        ProductCategory::create($this->validatedData($request));

        return redirect()->route('admin.purchase-orders.product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    public function edit(ProductCategory $productCategory)
    {
        return view('admin.product_categories.form', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $productCategory->update($this->validatedData($request, $productCategory->id));

        return redirect()->route('admin.purchase-orders.product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        if ($productCategory->products()->exists()) {
            return redirect()->route('admin.purchase-orders.product-categories.index')
                ->with('error', 'This category is linked to one or more products and cannot be deleted.');
        }

        $productCategory->delete();

        return redirect()->route('admin.purchase-orders.product-categories.index')
            ->with('success', 'Product category deleted successfully.');
    }

    protected function validatedData(Request $request, ?int $productCategoryId = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120|unique:product_categories,slug,' . $productCategoryId,
            'description' => 'nullable|string',
            'monthly_budget' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        return $data;
    }
}
