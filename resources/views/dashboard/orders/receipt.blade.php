@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-0">ANDA PETSHOP</h4>
                            <p class="text-muted small">Jl. Raya Petshop No. 123, Jakarta</p>
                            <hr class="border-dashed">
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <p class="mb-1 text-muted small">Invoice</p>
                                <h6 class="fw-bold">{{ $order->invoice_number }}</h6>
                            </div>
                            <div class="col-6 text-end">
                                <p class="mb-1 text-muted small">Tanggal</p>
                                <h6>{{ $order->created_at->format('d/m/Y H:i') }}</h6>
                            </div>
                        </div>

                        <table class="table table-borderless">
                            <thead class="border-bottom">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td class="text-center">{{ $item->qty }}</td>
                                        <td class="text-end">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <th colspan="3" class="text-end pt-3">Total</th>
                                    <th class="text-end pt-3 text-primary h5">
                                        Rp{{ number_format($order->total_amount, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end text-muted small">Dibayar
                                        ({{ ucfirst($order->payment->payment_method) }})</th>
                                    <th class="text-end text-muted small">
                                        Rp{{ number_format($order->payment->paid_amount, 0, ',', '.') }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end text-muted small">Kembalian</th>
                                    <th class="text-end text-muted small">
                                        Rp{{ number_format($order->payment->change_amount, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-center mt-5">
                            <p class="small text-muted mb-0">Terima kasih telah berbelanja!</p>
                            <p class="tiny text-muted">Kasir: {{ $order->user->name }}</p>
                            <button onclick="window.print()" class="btn btn-outline-dark btn-sm mt-3 d-print-none">
                                <i class="fa fa-print me-1"></i> Cetak Struk
                            </button> <br>
                            <a href="{{ route('dashboard.orders.index') }}" class="btn btn-secondary btn-sm mt-2">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .d-print-none,
            .main-sidebar,
            .main-header,
            .footer {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection
