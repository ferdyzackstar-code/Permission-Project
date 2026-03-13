<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    function __construct()
    {
        // Pastikan nama permission sesuai dengan yang ada di database kamu
        $this->middleware('permission:supplier-list|supplier-create|supplier-edit|supplier-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:supplier-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:supplier-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:supplier-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('purchase_price', function ($row) {
                    return 'Rp ' . number_format($row->purchase_price, 0, ',', '.');
                })
                ->addColumn('action', function ($row) {
                    // Pakai data-target dengan ID unik untuk modal
                    $btn = '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalShowSupplier' . $row->id . '"><i class="fa fa-eye"></i> Show</button>';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#modalEditSupplier' . $row->id . '"><i class="fa fa-edit"></i> Edit</button>';
                    $btn .= '<form action="' . route('dashboard.suppliers.destroy', $row->id) . '" method="POST" style="display:inline">' . csrf_field() . method_field('DELETE') . '<button type="button" class="btn btn-danger btn-sm show_confirm"><i class="fa fa-trash"></i> Delete</button></form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $suppliers = Supplier::all();
        $product = Product::all();
        return view('dashboard.suppliers.index', compact('suppliers', 'product'));
    }

    public function store(Request $request): RedirectResponse
    {
        dd($request->all());
        $request->validate([
            'name' => 'required',
            'item_code' => 'required',
            'item_name' => 'required',
            'purchase_price' => 'required|numeric',
        ]);

        Supplier::create($request->all());

        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'item_code' => 'required',
            'item_name' => 'required',
            'purchase_price' => 'required|numeric',
        ]);

        $supplier->update($request->all());

        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
