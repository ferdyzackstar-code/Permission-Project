<?php

namespace App\Http\Controllers;

use App\Exports\ProductsImportTemplateExport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:product.index|product.create|product.edit|product.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product.delete', ['only' => ['destroy']]);
    }

    /* ══════════════════════════════════════════
       INDEX
    ══════════════════════════════════════════ */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category.parent'])->select('products.*');

            return DataTables::eloquent($products)
                ->addIndexColumn()

                // ── Foto ─────────────────────────────────
                ->addColumn('image', function (Product $product) {
                    $path = 'storage/uploads/products/' . $product->image;
                    $url = $product->image && file_exists(public_path($path)) ? asset($path) : asset('storage/uploads/products/default-product.jpg');

                    return '<img src="' . $url . '" class="tbl-img" alt="' . e($product->name) . '">';
                })

                // ── Species ───────────────────────────────
                ->addColumn('species', function (Product $product) {
                    $name = optional(optional($product->category)->parent)->name ?? '—';
                    return '<span class="tbl-pill tbl-pill-species">' . e($name) . '</span>';
                })

                // ── Category ──────────────────────────────
                ->addColumn('category', function (Product $product) {
                    $name = optional($product->category)->name ?? '—';
                    return '<span class="tbl-pill tbl-pill-category">' . e($name) . '</span>';
                })

                // ── Status ────────────────────────────────
                ->addColumn('status', function (Product $product) {
                    $isActive = $product->status === 'active';
                    $cls = $isActive ? 'badge-active' : 'badge-inactive';
                    $dot = $isActive ? '🟢' : '🔴';
                    return '<span class="badge-status ' . $cls . '">' . $dot . ' ' . ucfirst($product->status) . '</span>';
                })

                // ── Harga ─────────────────────────────────
                ->addColumn('price', function (Product $product) {
                    return '<span class="tbl-price">Rp ' . number_format($product->price ?? 0, 0, ',', '.') . '</span>';
                })

                // ── Stok ──────────────────────────────────
                ->addColumn('stock', function (Product $product) {
                    $stock = $product->stock ?? 0;
                    [$cls, $icon] = match (true) {
                        $stock === 0 => ['stock-zero', 'fa-times-circle'],
                        $stock <= 5 => ['stock-low', 'fa-exclamation-circle'],
                        default => ['stock-ok', 'fa-check-circle'],
                    };
                    return '<span class="stock-pill ' . $cls . '"><i class="fas ' . $icon . '"></i> ' . $stock . ' Pcs</span>';
                })

                // ── Action ────────────────────────────────
                ->addColumn('action', function (Product $product) {
                    // Siapkan image URL untuk panel show/edit
                    $imgPath = 'storage/uploads/products/' . $product->image;
                    $imgUrl = $product->image && file_exists(public_path($imgPath)) ? asset($imgPath) : asset('storage/uploads/products/default-product.jpg');

                    // Encode semua data produk sebagai JSON untuk dikirim ke JS
                    $data = htmlspecialchars(
                        json_encode([
                            'id' => $product->id,
                            'name_raw' => $product->name,
                            'status_raw' => $product->status,
                            'price_raw' => $product->price,
                            'stock_raw' => $product->stock,
                            'detail' => $product->detail,
                            'species_id' => optional(optional($product->category)->parent)->id,
                            'species_raw' => optional(optional($product->category)->parent)->name ?? '—',
                            'category_id' => $product->category_id,
                            'category_raw' => optional($product->category)->name ?? '—',
                            'image_url' => $imgUrl,
                        ]),
                        ENT_QUOTES,
                    );

                    $btn = '<div class="tbl-actions">';

                    if (auth()->user()->can('product.show')) {
                        $btn .=
                            '<button type="button" class="tbl-btn tbl-btn-view btn-view-product"
                                    data-product=\'' .
                            $data .
                            '\'
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                 </button>';
                    }

                    if (auth()->user()->can('product.edit')) {
                        $btn .=
                            '<button type="button" class="tbl-btn tbl-btn-edit btn-edit-product"
                                    data-product=\'' .
                            $data .
                            '\'
                                    title="Edit Produk">
                                    <i class="fas fa-pencil-alt"></i>
                                 </button>';
                    }

                    if (auth()->user()->can('product.delete')) {
                        $btn .=
                            '<form action="' .
                            route('dashboard.products.destroy', $product->id) .
                            '"
                                       method="POST" style="display:inline">
                                    ' .
                            csrf_field() .
                            method_field('DELETE') .
                            '
                                    <button type="button"
                                        class="tbl-btn tbl-btn-delete btn-delete-product"
                                        data-name="' .
                            e($product->name) .
                            '"
                                        title="Hapus Produk">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                 </form>';
                    }

                    $btn .= '</div>';
                    return $btn;
                })

                ->rawColumns(['image', 'species', 'category', 'status', 'price', 'stock', 'action'])
                ->make(true);
        }

        $categories = Category::with('children')->whereNull('parent_id')->get();
        $products = Product::with(['category.parent'])->get();

        return view('dashboard.products.index', compact('categories', 'products'));
    }

    /* ══════════════════════════════════════════
       STORE
    ══════════════════════════════════════════ */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required',
            'stock' => 'required|integer|min:0',
            'detail' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['price'] = (int) str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), $request->name);
        }

        Product::create($data);

        // PLAIN TEXT — tanpa HTML tag, agar aman di semua konteks (blade & notif browser)
        $plainName = $request->name;
        return redirect()
            ->route('dashboard.products.index')
            ->with('success', "Produk \"{$plainName}\" berhasil ditambahkan.");
    }

    /* ══════════════════════════════════════════
       UPDATE
    ══════════════════════════════════════════ */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required',
            'stock' => 'required|integer|min:0',
            'detail' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['price'] = (int) str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            $this->deleteImage($product->image);
            $data['image'] = $this->uploadImage($request->file('image'), $request->name);
        }

        $product->update($data);

        $plainName = $product->name;
        return redirect()
            ->route('dashboard.products.index')
            ->with('success', "Produk \"{$plainName}\" berhasil diperbarui.");
    }

    /* ══════════════════════════════════════════
       DESTROY
    ══════════════════════════════════════════ */
    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteImage($product->image);
        $product->delete();

        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    /* ══════════════════════════════════════════
       GET SUB-CATEGORIES (AJAX)
    ══════════════════════════════════════════ */
    public function getSubCategories(int $parentId)
    {
        $subCategories = Category::where('parent_id', $parentId)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json($subCategories);
    }

    /* ══════════════════════════════════════════
       IMPORT / EXPORT / TEMPLATE
    ══════════════════════════════════════════ */
    public function downloadImportTemplate()
    {
        $categories = Category::orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL')->get();
        return Excel::download(new ProductsImportTemplateExport($categories), 'template_import_data_products.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);

        $import = new \App\Imports\ProductsImport();
        $import->import($request->file('file'));

        $failures = $import->getFailures();
        $importedCount = $import->getImportedCount();

        if (!empty($failures)) {
            return back()->with('import_failures', $failures)->with('import_total_failed', count($failures))->with('import_total_success', $importedCount);
        }

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', "Import berhasil! {$importedCount} produk berhasil ditambahkan.");
    }

    public function export()
    {
        $fileName = 'data_products_anda_petshop_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new \App\Exports\ProductsExport(), $fileName);
    }

    /* ══════════════════════════════════════════
       PRIVATE HELPERS
    ══════════════════════════════════════════ */
    private function uploadImage($file, string $productName): string
    {
        $dest = public_path('storage/uploads/products');
        if (!File::isDirectory($dest)) {
            File::makeDirectory($dest, 0755, true, true);
        }
        $filename = time() . '-' . Str::slug($productName) . '.' . $file->getClientOriginalExtension();
        $file->move($dest, $filename);
        return $filename;
    }

    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }
        $path = public_path('storage/uploads/products/' . $image);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
