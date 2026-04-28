<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    /* function __construct()
    {
        $this->middleware('permission:purchase.index|purchase.create|purchase.edit|purchase.delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:purchase.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:purchase.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:purchase.delete', ['only' => ['destroy']]);
    } */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $purchases = Purchase::with(['supplier'])->select('purchases.*');

            return DataTables::eloquent($purchases)
                ->addIndexColumn()
                ->addColumn('supplier_name', function ($row) {
                    return $row->supplier ? $row->supplier->name : '<span class="text-danger">No Supplier</span>';
                })
                ->addColumn('purchase_date', function ($row) {
                    return $row->purchase_date ? date('d-m-Y', strtotime($row->purchase_date)) : '-';
                })
                ->addColumn('total', function ($row) {
                    return 'Rp ' . number_format($row->total_amount ?? 0, 0, ',', '.');
                })
                ->addColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'pending' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary',
                    };
                    return '<span class="badge badge-' . $color . '">' . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#modalShowPurchase' . $row->id . '"><i class="fa fa-eye"></i></button>';
                    $btn .= '<button type="button" class="btn btn-primary btn-sm mr-1" data-toggle="modal" data-target="#modalEditPurchase' . $row->id . '"><i class="fa fa-edit"></i></button>';
                    $btn .= '<form action="' . route('dashboard.purchases.destroy', $row->id) . '" method="POST" style="display:inline">' . csrf_field() . method_field('DELETE') . '<button type="button" class="btn btn-danger btn-sm show_confirm"><i class="fa fa-trash"></i></button></form>';
                    return $btn;
                })
                ->rawColumns(['supplier_name', 'status', 'action'])
                ->make(true);
        }

        $purchases = Purchase::all();
        return view('dashboard.purchases.index', compact('purchases'));
    }

    public function create()
    {
        // PENTING: Filter hanya active suppliers
        $suppliers = Supplier::where('status', 'active')->get();
        $products = Product::all();

        return view('dashboard.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        // Validasi: supplier harus active
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                function ($attribute, $value, $fail) {
                    $supplier = Supplier::find($value);
                    if ($supplier && $supplier->status !== 'active') {
                        $fail('Supplier harus dalam status aktif untuk melakukan pembelian.');
                    }
                },
            ],
            'purchase_date' => 'required|date',
            'purchase_number' => 'required|unique:purchases,purchase_number',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,received,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'purchase_number' => trim($validated['purchase_number']),
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? 'pending',
            ]);

            DB::transaction(function () use ($purchase, $validated) {
                foreach ($validated['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['price'];

                    \App\Models\PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => (float) $item['price'],
                        'subtotal' => $subtotal,
                    ]);
                }
            });

            return redirect()->route('dashboard.purchases.index')->with('success', 'Pembelian berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pembelian: ' . $e->getMessage());
        }
    }

    public function edit(Purchase $purchase)
    {
        // Filter hanya active suppliers (kecuali current supplier purchase)
        $suppliers = Supplier::where('status', 'active')
            ->orWhere('id', $purchase->supplier_id) // Include current supplier
            ->get();
        $products = Product::all();

        return view('dashboard.purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                function ($attribute, $value, $fail) {
                    $supplier = Supplier::find($value);
                    if ($supplier && $supplier->status !== 'active') {
                        $fail('Supplier harus dalam status aktif untuk melakukan pembelian.');
                    }
                },
            ],
            'purchase_date' => 'required|date',
            'purchase_number' => 'required|unique:purchases,purchase_number,' . $purchase->id,
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,received,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }

            $purchase->update([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'purchase_number' => trim($validated['purchase_number']),
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);

            DB::transaction(function () use ($purchase, $validated) {
                // Hapus items lama
                $purchase->items()->delete();

                // Buat items baru
                foreach ($validated['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['price'];

                    \App\Models\PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => (float) $item['price'],
                        'subtotal' => $subtotal,
                    ]);
                }
            });

            return redirect()->route('dashboard.purchases.index')->with('success', 'Pembelian berhasil diupdate.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat update pembelian: ' . $e->getMessage());
        }
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->items()->delete();
        $purchase->delete();
        return redirect()->route('dashboard.purchases.index')->with('success', 'Pembelian berhasil dihapus.');
    }
}
