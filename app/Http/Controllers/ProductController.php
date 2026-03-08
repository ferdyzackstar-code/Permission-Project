<?php

namespace App\Http\Controllers;

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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with('category');

            return DataTables::eloquent($products)
                ->addIndexColumn()
                ->addColumn('category', function (Product $product) {
                    return $product->category->name ?? 'Tanpa Kategori';
                })
                ->editColumn('branch_name', function (Product $product) {
                    return 'Anda Petshop ' . $product->branch_name;
                })
                ->editColumn('detail', function (Product $product) {
                    return Str::limit($product->detail, 50);
                })
                ->addColumn('action', function (Product $product) {
                    $buttons =
                        '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalShowProduct' .
                        $product->id .
                        '">
                                    <i class="fa fa-eye"></i> Show
                                </button>';

                    if (Gate::allows('product-edit')) {
                        $buttons .=
                            '<button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#modalEditProduct' .
                            $product->id .
                            '">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>';
                    }

                    if (Gate::allows('product-delete')) {
                        $buttons .=
                            '<form method="POST" action="' .
                            route('dashboard.products.destroy', $product->id) .
                            '" class="delete-form" style="display:inline;">
                                ' .
                            csrf_field() .
                            '
                                <input name="_method" type="hidden" value="DELETE">
                                <button type="button" class="btn btn-icon btn-danger btn-sm show_confirm" data-id="' .
                            $product->id .
                            '" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>';
                    }

                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $categories = Category::with('children')->whereNull('parent_id')->get();
        $products = Product::with('category')->get();

        return view('dashboard.products.index', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        return view('dashboard.products.index', [
            'categories' => Category::with('children')->whereNull('parent_id')->get(),
            'products' => Product::with('category')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'branch_name' => 'required',
            'detail' => 'required',
        ]);

        \App\Models\Product::create($request->all());

        return redirect()->route('dashboard.products.index')->with('success', 'Produk berhasil ditambahkan ke cabang tersebut.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product): View
    {
        return view('dashboard.products.index', [
            'categories' => Category::with('children')->whereNull('parent_id')->get(),
            'products' => Product::with('category')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product): View
    {
        return view('dashboard.products.index', [
            'categories' => Category::with('children')->whereNull('parent_id')->get(),
            'products' => Product::with('category')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'detail' => 'required',
            'branch_name' => 'required',
        ]);

        $product->update($request->all());

        return redirect()->route('dashboard.products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('dashboard.products.index')->with('success', 'Product deleted successfully');
    }
}
