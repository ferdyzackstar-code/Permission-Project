<?php

namespace App\Http\Controllers;

use App\Exports\ProductsImportTemplateExport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
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
       INDEX — DataTable server-side
    ══════════════════════════════════════════ */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category.parent'])->select('products.*');

            return DataTables::eloquent($products)
                ->addIndexColumn()

                // ── Foto produk ───────────────────────────
                ->addColumn('image', function (Product $product) {
                    $path = 'storage/uploads/products/' . $product->image;
                    $url = $product->image && file_exists(public_path($path)) ? asset($path) : asset('storage/uploads/products/default-product.jpg');

                    return '<img src="' . $url . '" class="tbl-product-img" alt="' . e($product->name) . '">';
                })

                // ── Species (parent category) ─────────────
                ->addColumn('species', function (Product $product) {
                    $name = optional(optional($product->category)->parent)->name ?? '—';
                    return '<span style="
                        display:inline-block;font-size:.72rem;font-weight:700;
                        padding:.28em .75em;border-radius:2rem;
                        background:rgba(78,115,223,.1);color:#4e73df;
                        border:1px solid rgba(78,115,223,.2)">' .
                        e($name) .
                        '</span>';
                })

                // ── Category (child) ──────────────────────
                ->addColumn('category', function (Product $product) {
                    $name = optional($product->category)->name ?? '—';
                    return '<span style="
                        display:inline-block;font-size:.72rem;font-weight:700;
                        padding:.28em .75em;border-radius:2rem;
                        background:rgba(54,185,204,.1);color:#258391;
                        border:1px solid rgba(54,185,204,.2)">' .
                        e($name) .
                        '</span>';
                })

                // ── Status badge ──────────────────────────
                ->addColumn('status', function (Product $product) {
                    $isActive = $product->status === 'active';
                    [$cls, $dot] = $isActive ? ['badge-active', '🟢'] : ['badge-inactive', '🔴'];

                    return '<span class="badge-status ' . $cls . '">' . $dot . ' ' . ucfirst($product->status) . '</span>';
                })

                // ── Harga ─────────────────────────────────
                ->addColumn('price', function (Product $product) {
                    return '<span style="font-weight:700;color:#1cc88a;white-space:nowrap">Rp ' . number_format($product->price ?? 0, 0, ',', '.') . '</span>';
                })

                // ── Stok indicator ────────────────────────
                ->addColumn('stock', function (Product $product) {
                    $stock = $product->stock ?? 0;

                    if ($stock === 0) {
                        $cls = 'stock-zero';
                        $icon = 'fa-times-circle';
                    } elseif ($stock <= 5) {
                        $cls = 'stock-low';
                        $icon = 'fa-exclamation-circle';
                    } else {
                        $cls = 'stock-ok';
                        $icon = 'fa-check-circle';
                    }

                    return '<span class="stock-pill ' .
                        $cls .
                        '">
                                <i class="fas ' .
                        $icon .
                        '"></i> ' .
                        $stock .
                        ' Pcs
                            </span>';
                })

                // ── Action buttons ────────────────────────
                ->addColumn('action', function (Product $product) {
                    $btn = '<div class="tbl-actions">';

                    // View
                    if (auth()->user()->can('product.show')) {
                        $btn .=
                            '<button type="button"
                                    class="tbl-btn tbl-btn-view"
                                    data-toggle="modal"
                                    data-target="#showView' .
                            $product->id .
                            '"
                                    title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                 </button>';
                    }

                    // Edit
                    if (auth()->user()->can('product.edit')) {
                        $btn .= 
                            '<button type="button"
                                    class="tbl-btn tbl-btn-edit"
                                    data-toggle="modal"
                                    data-target="#productForm' .
                            $product->id .
                            '"
                                    title="Edit Produk">
                                    <i class="fas fa-pencil-alt"></i>
                                 </button>';
                    }

                    // Delete
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

        // Non-AJAX: pass data untuk modal & dropdown
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
            'detail' => 'required|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['price'] = (int) str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), $request->name);
        }

        Product::create($data);

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Produk <strong>' . e($request->name) . '</strong> berhasil ditambahkan.');
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
            'detail' => 'required|string',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->all();
        $data['price'] = (int) str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            // Hapus foto lama
            $this->deleteImage($product->image);
            $data['image'] = $this->uploadImage($request->file('image'), $request->name);
        }

        $product->update($data);

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', 'Produk <strong>' . e($product->name) . '</strong> berhasil diperbarui.');
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
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new \App\Imports\ProductsImport();
        $import->import($request->file('file'));

        $failures = $import->getFailures();
        $importedCount = $import->getImportedCount();

        if (!empty($failures)) {
            return back()->with('import_failures', $failures)->with('import_total_failed', count($failures))->with('import_total_success', $importedCount);
        }

        return redirect()
            ->route('dashboard.products.index')
            ->with('success', "Import berhasil! <strong>{$importedCount}</strong> produk berhasil ditambahkan.");
    }

    public function export()
    {
        $fileName = 'data_products_anda_petshop_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new \App\Exports\ProductsExport(), $fileName);
    }

    /* ══════════════════════════════════════════
       PRIVATE HELPERS
    ══════════════════════════════════════════ */

    /**
     * Upload foto produk dan kembalikan filename.
     */
    private function uploadImage($file, string $productName): string
    {
        $destinationPath = public_path('storage/uploads/products');

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }

        $filename = time() . '-' . Str::slug($productName) . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $filename);

        return $filename;
    }

    /**
     * Hapus foto produk dari storage (jika ada).
     */
    private function deleteImage(?string $image): void
    {
        if (!$image) {
            return;
        }

        $filePath = public_path('storage/uploads/products/' . $image);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }
}
