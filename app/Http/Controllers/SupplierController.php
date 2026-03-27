<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Supplier::withCount('products')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product_qty', function ($row) {
                    return '<span class="badge badge-info shadow-sm">' . $row->products_count . ' Produk</span>';
                })
                ->addColumn('status', function ($row) {
                    $color = $row->status == 'active' ? 'success' : 'danger';
                    return '<span class="badge badge-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-info btn-sm btn-show" data-id="' .
                        $row->id .
                        '">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button class="btn btn-primary btn-sm btn-edit" data-id="' .
                        $row->id .
                        '">
                            <i class="fa fa-edit"></i>
                        </button>
                        <form action="' .
                        route('dashboard.suppliers.destroy', $row->id) .
                        '" method="POST" style="display:inline">
                            ' .
                        csrf_field() .
                        method_field('DELETE') .
                        '
                            <button type="submit" class="btn btn-danger btn-sm show_confirm">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>';
                })
                ->rawColumns(['product_qty', 'status', 'action'])
                ->make(true);
        }

        $suppliers = Supplier::withCount('products')->get();

        return view('dashboard.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'status' => 'required',
            'email' => 'nullable|email',
            'city' => 'nullable',
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        Supplier::create($data);
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier berhasil ditambah.');
    }

    public function edit(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $supplier->update($request->all());
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier berhasil diupdate.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('dashboard.suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
