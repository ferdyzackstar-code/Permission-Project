@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-4 receipt-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-0">ANDA PETSHOP</h4>
                            <p class="text-muted small mb-0">Jl. Raya Petshop No. 123, Jakarta</p>
                            <div class="border-bottom border-dashed my-3"></div>
                        </div>

                        <div class="receipt-info mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">No. Invoice:</span>
                                <span class="fw-bold">{{ $order->invoice_number }}</span>
                            </div>  
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Tanggal:</span>
                                <span>{{ $order->created_at->format('d/m/Y H.i.s') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Kasir:</span>
                                <span>{{ $order->user->name }}</span>
                            </div>
                        </div>

                        <div class="border-bottom border-dashed mb-3"></div>

                        <table class="table table-borderless table-sm mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th style="width: 50%;" class="pb-2">Item</th>
                                    <th style="width: 20%;" class="text-center pb-2">Qty</th>
                                    <th style="width: 30%;" class="text-right pb-2">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-wrap" style="max-width: 200px;">
                                                {{ $item->product->name }}</div>
                                            <div class="tiny text-muted">Rp{{ number_format($item->price, 0, ',', '.') }}
                                            </div>   
                                        </td>
                                        <td class="text-center align-middle py-3">{{ $item->qty }}</td>
                                        <td class="text-right align-middle py-3 font-weight-bold">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="border-bottom border-dashed my-3"></div>

                        <div class="receipt-summary">
                            <div class="d-flex justify-content-between mb-1 h5">
                                <span class="font-weight-bold">Total</span>
                                <span
                                    class="font-weight-bold text-primary">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1 text-muted small">
                                <span>Bayar ({{ ucfirst($order->payment->payment_method) }})</span>
                                <span>Rp{{ number_format($order->payment->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted small">   
                                <span>Kembalian</span>
                                <span>Rp{{ number_format($order->payment->change_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <p class="small text-muted mb-0">Terima kasih telah mempercayakan</p>
                            <p class="small text-muted">kebutuhan anabul Anda kepada kami!</p>

                            <div class="d-print-none mt-4">
                                <button onclick="window.print()" class="btn btn-dark btn-sm px-4">
                                    <i class="fa fa-print me-1"></i> Cetak Struk
                                </button>
                                <a href="{{ route('dashboard.orders.index') }}"
                                    class="btn btn-outline-secondary btn-sm px-4">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styling khusus struk agar mirip gambar */
        .receipt-card {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            border-radius: 15px;
        }

        .border-dashed {
            border-bottom: 1.5px dashed #dee2e6 !important;
        }

        .tiny {
            font-size: 0.75rem;
        }

        .receipt-info span,
        .receipt-summary span {
            font-size: 0.9rem;
        }

        @media print {
            body {
                background-color: white !important;
            }

            .main-sidebar,
            .main-header,
            .footer,
            .d-print-none,
            .content-header {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
            }

            .container-fluid {
                width: 100% !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
            }

            /* Menghilangkan margin browser saat print */
            @page {
                margin: 0.5cm;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const invoiceNumber = urlParams.get('invoice');

            if (status === 'success' && invoiceNumber && invoiceNumber !== 'null') {
                Swal.fire({
                    title: "Transaksi Berhasil!",
                    html: `No Invoice: <b>${invoiceNumber}</b>`,
                    icon: "success",
                    timer: 3000,
                    showConfirmButton: false
                });
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
@endpush
