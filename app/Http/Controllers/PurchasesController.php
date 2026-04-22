<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PurchaseController extends Controller
{
    public function index(Request $request) 
    {
        if ($request->ajax()) {
            $data = Purchase::with('supplier')->select('purchases.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('purchase_date', function ($row) {
                    return Carbon::parse($row->purchase_date)->format('d M Y');
                })
                ->editColumn('status', function ($row) {
                    $class = $row->status == 'received' ? 'bg-success' : 'bg-warning';
                    return '<span class="badge ' . $class . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="btn-group">
                            <button class="btn btn-sm btn-warning edit-btn" data-id="' .
                        $row->id .
                        '">Edit</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="' .
                        $row->id .
                        '">Delete</button>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $suppliers = Supplier::all();
        return view('purchases.index', compact('suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('purchases.create', compact('suppliers'));
    }

    public function show($id)
    {
        $purchase = Purchase::with('supplier')->findOrFail($id);
        return view('purchases.show', compact('purchase'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
        ]);

        $datePrefix = Carbon::parse($request->purchase_date)->format('Ymd');
        $lastPurchase = Purchase::whereDate('purchase_date', $request->purchase_date)->latest('id')->first();
        $sequence = $lastPurchase ? intval(substr($lastPurchase->purchase_number, -4)) + 1 : 1;
        $purchaseNumber = 'PO-' . $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        $purchase = Purchase::create([
            'supplier_id' => $request->supplier_id,
            'purchase_date' => $request->purchase_date,
            'purchase_number' => $purchaseNumber,
            'total_amount' => 0,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Purchase created successfully!',
            'data' => $purchase->load('supplier'),
        ]);
    }

    public function edit($id)
    {
        $purchase = Purchase::findOrFail($id);
        return response()->json($purchase);
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Purchase updated successfully!',
            'data' => $purchase->load('supplier'),
        ]);
    }

    public function destroy($id)
    {
        Purchase::destroy($id);
        return response()->json(['success' => true]);
    }
}
