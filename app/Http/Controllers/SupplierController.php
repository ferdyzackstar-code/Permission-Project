<?php

namespace App\Http\Controllers;

use App\Exports\SuppliersImportTemplateExport;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:supplier.index|supplier.create|supplier.edit|supplier.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:supplier.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:supplier.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:supplier.delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::latest();

            return DataTables::of($data)
                ->addIndexColumn()
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
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $suppliers = Supplier::all();

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

    public function downloadImportTemplate()
    {
        return Excel::download(new SuppliersImportTemplateExport(), 'template_import_data_suppliers.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $file = $request->file('file');
        $import = new \App\Imports\SuppliersImport();

        $import->import($file);

        if ($import->failures()->isNotEmpty()) {
            return back()->with('import_failures', $import->failures());
        }

        return redirect()->route('dashboard.suppliers.index')->with('success', 'Data berhasil diimport!');
    }

    public function export()
    {
        $suppliers = Supplier::all();

        $fileName = 'data_suppliers_anda_petshop_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new \App\Exports\SuppliersExport($suppliers), $fileName);
    }
}
