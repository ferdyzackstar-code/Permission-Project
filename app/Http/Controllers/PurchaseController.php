<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Purchase::with('supplier')->select('purchases.*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('purchase_date', fn($row) => Carbon::parse($row->purchase_date)->format('d/m/Y'))
                ->editColumn('status', function ($row) {
                    $color = $row->status == 'received' ? 'success' : 'warning';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-info text-white show-btn" data-id="' .
                        $row->id .
                        '">Detail</button>
                        <button class="btn btn-sm btn-warning edit-btn" data-id="' .
                        $row->id .
                        '">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' .
                        $row->id .
                        '">Delete</button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $suppliers = Supplier::all();
        return view('dashboard.purchases.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'purchase_date' => 'required|date',
        ]);

        $datePrefix = Carbon::parse($request->purchase_date)->format('Ymd');
        $last = Purchase::whereDate('purchase_date', $request->purchase_date)->latest('id')->first();
        $seq = $last ? intval(substr($last->purchase_number, -4)) + 1 : 1;

        $purchase = Purchase::create([
            'supplier_id' => $request->supplier_id,
            'purchase_date' => $request->purchase_date,
            'purchase_number' => 'PO-' . $datePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT),
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json(['success' => 'Purchase created!']);
    }

    public function show($id)
    {
        // Load relasi agar data supplier muncul di modal detail
        return response()->json(Purchase::with('supplier')->findOrFail($id));
    }

    public function edit($id)
    {
        return response()->json(Purchase::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->update($request->all());
        return response()->json(['success' => 'Purchase updated!']);
    }

    public function destroy($id)
    {
        Purchase::destroy($id);
        return response()->json(['success' => 'Deleted!']);
    }
}
