<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'outlet'])->select('products.*'); // Tambahkan select agar query jelas

            return DataTables::eloquent($products)
                ->addIndexColumn()
                ->addColumn('category', function (Product $product) {
                    return $product->category->name ?? '-';
                })
                ->addColumn('outlet_name', function (Product $product) {
                    // Ubah jadi outlet_name agar tidak bentrok
                    return $product->outlet->name ?? '-';
                })
                ->filterColumn('outlet_name', function ($query, $keyword) {
                    // Samakan namanya
                    $query->whereHas('outlet', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('price', function (Product $product) {
                    return 'Rp ' . number_format($product->price ?? 0, 0, ',', '.');
                })
                ->addColumn('stock', function (Product $product) {
                    return ($product->stock ?? 0) . ' Pcs';
                })
                ->addColumn('action', function (Product $product) {
                    $btn = '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalShowProduct' . $product->id . '"><i class="fa fa-eye"></i> Show</button>';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#modalEditProduct' . $product->id . '"><i class="fa fa-edit"></i> Edit</button>';
                    $btn .= '<form action="' . route('dashboard.products.destroy', $product->id) . '" method="POST" style="display:inline">' . csrf_field() . method_field('DELETE') . '<button type="button" class="btn btn-danger btn-sm show_confirm"><i class="fa fa-trash"></i> Delete</button></form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $outlets = Outlet::all();
        $categories = Category::with('children')->whereNull('parent_id')->get();
        $products = Product::all();

        return view('dashboard.products.index', compact('categories', 'products', 'outlets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'outlet_id' => 'required',
            'price' => 'required',
            'stock' => 'required|integer',
            'detail' => 'required',
        ]);

        Product::create($request->all());

        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'outlet_id' => 'required',
            'price' => 'required',
            'stock' => 'required|integer',
            'detail' => 'required',
        ]);

        $product->update($request->all());

        return redirect()->route('dashboard.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('dashboard.products.index')->with('success', 'Product deleted successfully');
    }
}
