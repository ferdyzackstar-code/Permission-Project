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
    function __construct()
    {
        $this->middleware('permission:product.index|product.create|product.edit|product.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category'])->select('products.*');

            return DataTables::eloquent($products)
                ->addIndexColumn()
                ->addColumn('image', function (Product $product) {
                    $path = 'storage/uploads/products/' . $product->image;
                    $url = $product->image && file_exists(public_path($path)) ? asset($path) : asset('storage/uploads/products/default-product.jpg');

                    return '<img src="' . $url . '" width="50" class="img-thumbnail shadow-sm">';
                })
                ->addColumn('status', function (Product $product) {
                    $badge = $product->status == 'active' ? 'success' : 'danger';
                    return '<span class="badge badge-' . $badge . '">' . ucfirst($product->status) . '</span>';
                })
                ->addColumn('species', function (Product $product) {
                    return $product->category->parent->name ?? '-';
                })
                ->addColumn('category', function (Product $product) {
                    return $product->category->name ?? '-';
                })
                ->addColumn('price', function (Product $product) {
                    return 'Rp ' . number_format($product->price ?? 0, 0, ',', '.');
                })
                ->addColumn('action', function (Product $product) {
                    $btn = '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalShowProduct' . $product->id . '"><i class="fa fa-eye"></i></button>';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#modalEditProduct' . $product->id . '"><i class="fa fa-edit"></i></button>';
                    $btn .= '<form action="' . route('dashboard.products.destroy', $product->id) . '" method="POST" style="display:inline">' . csrf_field() . method_field('DELETE') . '<button type="button" class="btn btn-danger btn-sm show_confirm"><i class="fa fa-trash"></i></button></form>';
                    return $btn;
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }

        $categories = Category::with('children')->whereNull('parent_id')->get();
        $products = Product::all();

        return view('dashboard.products.index', compact('categories', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'stock' => 'required|integer',
            'detail' => 'required',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $data['price'] = str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '-' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('storage/uploads/products');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $data['image'] = $filename;
        }

        Product::create($data);
        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required',
            'stock' => 'required|integer',
            'detail' => 'required',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $data['price'] = str_replace('.', '', $request->price);

        if ($request->hasFile('image')) {
            $destinationPath = public_path('storage/uploads/products');

            if ($product->image && File::exists($destinationPath . '/' . $product->image)) {
                File::delete($destinationPath . '/' . $product->image);
            }

            $file = $request->file('image');
            $filename = time() . '-' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);
            $data['image'] = $filename;
        }

        $product->update($data);
        return redirect()->route('dashboard.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            $oldFilePath = public_path('storage/uploads/products/' . $product->image);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }

        $product->delete();
        return redirect()->route('dashboard.products.index')->with('success', 'Product deleted successfully');
    }

    public function getSubCategories($parentId)
    {
        $subCategories = \App\Models\Category::where('parent_id', $parentId)
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json($subCategories);
    }

    public function downloadImportTemplate()
    {
        $categories = \App\Models\Category::orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL')->get();

        return Excel::download(new ProductsImportTemplateExport($categories), 'template_import_data_products.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
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
            ->with('success', "Import berhasil! {$importedCount} produk berhasil ditambahkan.");
    }

    public function export()
    {
        $fileName = 'data_products_anda_petshop_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new \App\Exports\ProductsExport(), $fileName);
    }
}
