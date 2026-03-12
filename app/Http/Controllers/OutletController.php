<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        // Menyesuaikan middleware dengan permission outlet kamu
        $this->middleware('permission:outlet-list|outlet-create|outlet-edit|outlet-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:outlet-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:outlet-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:outlet-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $outlets = Outlet::query();

            return DataTables::eloquent($outlets)
                ->addIndexColumn()
                ->editColumn('address', function (Outlet $outlet) {
                    // Menggunakan Str::limit agar alamat tidak merusak tata letak tabel
                    return Str::limit($outlet->address, 50);
                })
                ->editColumn('phone', function (Outlet $outlet) {
                    return $outlet->phone ?? 'Belum Diisi';
                })
                // Di dalam return DataTables::eloquent($outlets) bagian ->addColumn('action'...)
                ->addColumn('action', function (Outlet $outlet) {
                    $buttons = '<div class="d-flex justify-content-center">';

                    if (Gate::allows('outlet-show')) {
                        $buttons .=
                            '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#showOutletModal' .
                            $outlet->id .
                            '">
                        <i class="fas fa-eye"></i> Show
                    </button>';
                    }

                    if (Gate::allows('outlet-edit')) {
                        $buttons .=
                            '<button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#editOutletModal' .
                            $outlet->id .
                            '">
                        <i class="fas fa-edit"></i> Edit
                    </button>';
                    }

                    if (Gate::allows('outlet-delete')) {
                        $buttons .=
                            '<form action="' .
                            route('dashboard.outlets.destroy', $outlet->id) .
                            '" method="POST" class="delete-form" style="display:inline;">' .
                            csrf_field() .
                            '<input name="_method" type="hidden" value="DELETE">
                    <button type="button" class="btn btn-danger btn-sm btn-delete">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                    </form>';
                    }

                    $buttons .= '</div>';
                    return $buttons;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $outlets = Outlet::all();
        return view('dashboard.outlets.index', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'nullable|string|max:15',
        ]);

        Outlet::create($request->all());

        return redirect()->route('dashboard.outlets.index')->with('success', 'Data outlet berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'nullable|string|max:15',
        ]);

        $outlet = Outlet::findOrFail($id);
        $outlet->update($request->all());

        return redirect()->route('dashboard.outlets.index')->with('success', 'Data outlet berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $outlet = Outlet::findOrFail($id);
        $outlet->delete();

        return redirect()->route('dashboard.outlets.index')->with('success', 'Data outlet berhasil dihapus!');
    }
}
