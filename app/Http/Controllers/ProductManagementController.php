<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class ProductManagementController extends Controller
{
    public function index(Request $request)
    {
        $supplierSubquery = DB::table('category_suppliers')
            ->join('suppliers', 'suppliers.id', '=', 'category_suppliers.supplier_id')
            ->selectRaw('category_suppliers.category_id, MIN(suppliers.name) as preferred_supplier_name')
            ->groupBy('category_suppliers.category_id');

        $requestItemsSubquery = DB::table('request_items')
            ->selectRaw('product_id, COUNT(*) as request_lines_count, COALESCE(SUM(quantity), 0) as requested_quantity')
            ->groupBy('product_id');

        $poItemsSubquery = DB::table('po_items')
            ->selectRaw('product_id, COUNT(*) as po_lines_count, COALESCE(SUM(quantity), 0) as ordered_quantity')
            ->groupBy('product_id');

        $productsQuery = Product::query()
            ->with('category:id,name')
            ->leftJoinSub($supplierSubquery, 'preferred_suppliers', function ($join) {
                $join->on('preferred_suppliers.category_id', '=', 'products.category_id');
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
                    ->from('category_suppliers')
                    ->whereColumn('category_suppliers.category_id', 'products.category_id')
                    ->where('category_suppliers.supplier_id', $request->supplier);
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
            'catalog_linked_suppliers' => Schema::hasTable('category_suppliers')
                ? DB::table('category_suppliers')->distinct('supplier_id')->count('supplier_id')
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
            Product::create($data);
        });

        return redirect()->route('admin.purchase-orders.products')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', array_merge(
            $this->formData(),
            compact('product')
        ));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validatedData($request, $product->id);

        DB::transaction(function () use ($data, $product) {
            $product->update($data);
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
            $product->delete();
        });

        return redirect()->route('admin.purchase-orders.products')
            ->with('success', 'Product deleted successfully.');
    }

    public function export()
    {
        $products = Product::query()
            ->with('category:id,name')
            ->orderBy('name')
            ->get();

        $rows = $products->map(fn (Product $product) => [
            'name' => $product->name,
            'sku' => $product->sku,
            'category_id' => $product->category_id,
            'category_name' => $product->category?->name,
            'unit' => $product->unit,
            'estimated_price' => $product->estimated_price,
            'barcode' => $product->barcode,
            'status' => $product->status,
        ]);

        return $this->downloadExcel('products-export-' . now()->format('Y-m-d-His') . '.xlsx', $rows->all());
    }

    public function importForm()
    {
        return view('admin.purchase_orders.products.import', [
            'categoriesCount' => ProductCategory::count(),
            'productsCount' => Product::count(),
            'barcodeEnabledCount' => Product::whereNotNull('barcode')->where('barcode', '!=', '')->count(),
        ]);
    }

    public function sampleImport()
    {
        return $this->downloadExcel('products-import-sample.xlsx', [
            [
                'name' => 'Tomato-პომიდორი',
                'sku' => 'VEG-TOM-001',
                'category_id' => '',
                'category_name' => 'Vegetables',
                'unit' => 'kg',
                'estimated_price' => '3.50',
                'barcode' => '100000000001',
                'status' => 'active',
            ],
            [
                'name' => 'Chicken Breast',
                'sku' => 'MEAT-CHK-001',
                'category_id' => '',
                'category_name' => 'Meat',
                'unit' => 'kg',
                'estimated_price' => '18.00',
                'barcode' => '100000000002',
                'status' => 'active',
            ],
            [
                'name' => 'Mineral Water',
                'sku' => 'BEV-WAT-001',
                'category_id' => '',
                'category_name' => 'Beverages',
                'unit' => 'pcs',
                'estimated_price' => '1.20',
                'barcode' => '100000000003',
                'status' => 'active',
            ],
        ]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'products_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $rows = $this->readExcelRows($validated['products_file']->getRealPath());
        } catch (Throwable) {
            return redirect()->route('admin.purchase-orders.products.import.form')
                ->with('error', 'Unable to read the uploaded Excel file.');
        }

        $headerRow = array_shift($rows);

        if (! is_array($headerRow)) {
            return redirect()->route('admin.purchase-orders.products.import.form')
                ->with('error', 'The Excel file is empty.');
        }

        $headers = array_map(fn ($header) => $this->normalizeImportHeader((string) $header), $headerRow);
        $requiredHeaders = ['name', 'sku'];
        $missingHeaders = array_diff($requiredHeaders, $headers);

        if (! empty($missingHeaders)) {
            return redirect()->route('admin.purchase-orders.products.import.form')
                ->with('error', 'Missing required Excel columns: ' . implode(', ', $missingHeaders));
        }

        $created = 0;
        $updated = 0;
        $rowNumber = 1;
        $errors = [];

        DB::transaction(function () use ($rows, $headers, &$created, &$updated, &$rowNumber, &$errors) {
            foreach ($rows as $row) {
                $rowNumber++;
                $row = array_pad($row, count($headers), null);
                $data = array_combine($headers, array_slice($row, 0, count($headers)));

                if (! $data || collect($data)->filter(fn ($value) => trim((string) $value) !== '')->isEmpty()) {
                    continue;
                }

                $productData = $this->productDataFromCsvRow($data, $rowNumber, $errors);

                if ($productData === null) {
                    continue;
                }

                $existingProduct = Product::query()->where('sku', $productData['sku'])->first();

                Product::updateOrCreate(
                    ['sku' => $productData['sku']],
                    $productData
                );

                $existingProduct ? $updated++ : $created++;
            }
        });

        $message = "Products import finished. Created: {$created}. Updated: {$updated}.";

        if (! empty($errors)) {
            return redirect()->route('admin.purchase-orders.products.import.form')
                ->with('error', $message . ' Skipped rows: ' . implode(' | ', array_slice($errors, 0, 8)));
        }

        return redirect()->route('admin.purchase-orders.products')
            ->with('success', $message);
    }

    protected function formData(): array
    {
        return [
            'categories' => ProductCategory::orderBy('name')->get(['id', 'name']),
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
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')->ignore($productId),
            ],
            'status' => 'required|in:active,inactive',
            'estimated_price' => 'nullable|numeric|min:0',
        ]);

        $barcode = isset($validated['barcode']) ? trim((string) $validated['barcode']) : null;
        $barcode = $barcode === '' ? null : $barcode;

        return [
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'category_id' => $validated['category_id'] ?? null,
            'unit' => $validated['unit'] ?? null,
            'barcode' => $barcode,
            'status' => $validated['status'],
            'estimated_price' => $validated['estimated_price'] ?? null,
        ];
    }

    public function lookupBarcode(string $barcode)
    {
        $product = Product::query()
            ->with(['category:id,name', 'category.suppliers:id,name'])
            ->where('barcode', trim($barcode))
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found for this barcode.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->productLookupPayload($product),
        ]);
    }

    public function searchProducts(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        if ($search === '') {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $products = Product::query()
            ->with(['category:id,name', 'category.suppliers:id,name'])
            ->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%')
                    ->orWhere('barcode', 'like', '%' . $search . '%');
            })
            ->orderByRaw('CASE WHEN barcode = ? THEN 0 WHEN sku = ? THEN 1 ELSE 2 END', [$search, $search])
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(fn (Product $product) => $this->productLookupPayload($product));

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    protected function productLookupPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'category' => optional($product->category)->name ?: '-',
            'unit' => $product->unit ?: '',
            'preferred_supplier' => optional($product->category?->suppliers->first())->name ?: '-',
            'supplier_id' => optional($product->category?->suppliers->first())->id,
            'pack_size' => 'Standard',
            'barcode' => $product->barcode ?: '',
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
            ->with(['category:id,name', 'category.suppliers:id,name'])
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

    private function productDataFromCsvRow(array $row, int $rowNumber, array &$errors): ?array
    {
        $name = trim((string) ($row['name'] ?? ''));
        $sku = trim((string) ($row['sku'] ?? ''));
        $unit = trim((string) ($row['unit'] ?? ''));
        $barcode = trim((string) ($row['barcode'] ?? ''));
        $status = strtolower(trim((string) ($row['status'] ?? 'active')));
        $estimatedPrice = trim((string) ($row['estimated_price'] ?? ''));

        if ($name === '' || $sku === '') {
            $errors[] = "Row {$rowNumber}: name and sku are required.";

            return null;
        }

        if (mb_strlen($name) > 150 || mb_strlen($sku) > 50 || mb_strlen($unit) > 20 || mb_strlen($barcode) > 50) {
            $errors[] = "Row {$rowNumber}: one or more fields exceed allowed length.";

            return null;
        }

        if (! in_array($status, ['active', 'inactive'], true)) {
            $errors[] = "Row {$rowNumber}: status must be active or inactive.";

            return null;
        }

        if ($estimatedPrice !== '' && ! is_numeric($estimatedPrice)) {
            $errors[] = "Row {$rowNumber}: estimated_price must be numeric.";

            return null;
        }

        $categoryId = $this->categoryIdFromCsvRow($row);

        if ($categoryId === false) {
            $errors[] = "Row {$rowNumber}: category_id was not found.";

            return null;
        }

        $productId = Product::query()->where('sku', $sku)->value('id');

        if ($barcode !== '') {
            $barcodeExists = Product::query()
                ->where('barcode', $barcode)
                ->when($productId, fn ($query) => $query->whereKeyNot($productId))
                ->exists();

            if ($barcodeExists) {
                $errors[] = "Row {$rowNumber}: barcode {$barcode} is already used.";

                return null;
            }
        }

        return [
            'name' => $name,
            'sku' => $sku,
            'category_id' => $categoryId ?: null,
            'unit' => $unit !== '' ? $unit : null,
            'barcode' => $barcode !== '' ? $barcode : null,
            'status' => $status,
            'estimated_price' => $estimatedPrice !== '' ? $estimatedPrice : null,
        ];
    }

    private function categoryIdFromCsvRow(array $row): int|false|null
    {
        $categoryId = trim((string) ($row['category_id'] ?? ''));

        if ($categoryId !== '') {
            return ProductCategory::query()->whereKey($categoryId)->exists()
                ? (int) $categoryId
                : false;
        }

        $categoryName = trim((string) ($row['category_name'] ?? ''));

        if ($categoryName === '') {
            return null;
        }

        $existingCategoryId = ProductCategory::query()
            ->where('name', $categoryName)
            ->orWhere('slug', Str::slug($categoryName))
            ->value('id');

        if ($existingCategoryId) {
            return (int) $existingCategoryId;
        }

        return ProductCategory::create([
            'name' => $categoryName,
            'slug' => $this->categorySlugFromName($categoryName),
            'monthly_budget' => 0,
            'status' => 'active',
        ])->id;
    }

    private function categorySlugFromName(string $categoryName): string
    {
        $slug = Str::slug($categoryName);

        if ($slug !== '') {
            return $slug;
        }

        return 'category-' . substr(md5(Str::lower($categoryName)), 0, 10);
    }

    private function downloadCsv(string $fileName, array $rows)
    {
        $headers = ['name', 'sku', 'category_id', 'category_name', 'unit', 'estimated_price', 'barcode', 'status'];

        return response()->streamDownload(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);

            foreach ($rows as $row) {
                fputcsv($output, array_map(fn ($header) => $row[$header] ?? '', $headers));
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function downloadExcel(string $fileName, array $rows)
    {
        $headers = ['name', 'sku', 'category_id', 'category_name', 'unit', 'estimated_price', 'barcode', 'status'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');

        foreach ($headers as $columnIndex => $header) {
            $column = $columnIndex + 1;
            $sheet->setCellValueExplicit([$column, 1], $header, DataType::TYPE_STRING);
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        foreach ($rows as $rowIndex => $row) {
            $excelRow = $rowIndex + 2;

            foreach ($headers as $columnIndex => $header) {
                $sheet->setCellValueExplicit(
                    [$columnIndex + 1, $excelRow],
                    (string) ($row[$header] ?? ''),
                    DataType::TYPE_STRING
                );
            }
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function readExcelRows(string $path): array
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = [];
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();

        foreach ($sheet->rangeToArray("A1:{$highestColumn}{$highestRow}", '', false, false, false) as $row) {
            $rows[] = array_map(fn ($value) => is_null($value) ? '' : trim((string) $value), $row);
        }

        $spreadsheet->disconnectWorksheets();

        return $rows;
    }

    private function normalizeImportHeader(string $header): string
    {
        return Str::snake(trim(strtolower($header)));
    }
}
