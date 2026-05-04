@extends('dashboard.layouts.admin')

@section('title', 'Struk — ' . $order->invoice_number)

@push('styles')
    <style>
        :root {
            --rec-primary: #1565C0;
            --rec-radius: 14px;
        }

        .receipt-wrapper {
            max-width: 480px;
            margin: 0 auto;
            padding-bottom: 40px;
        }

        .receipt-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #546E7A;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: color .2s;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }

        .btn-back:hover {
            color: var(--rec-primary);
        }

        .btn-print {
            background: linear-gradient(135deg, #1565C0, #1976D2);
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 3px 12px rgba(21, 101, 192, .25);
            transition: all .2s;
        }

        .btn-print:hover {
            transform: translateY(-1px);
        }

        .receipt-card {
            background: #fff;
            border-radius: var(--rec-radius);
            box-shadow: 0 4px 24px rgba(21, 101, 192, .10);
            overflow: hidden;
        }

        .receipt-stripe {
            height: 6px;
            background: linear-gradient(90deg, #0D47A1, #42A5F5, #0D47A1);
            background-size: 200% 100%;
            animation: shimmer 2.5s linear infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: 0%
            }

            100% {
                background-position: 200%
            }
        }

        .receipt-body {
            padding: 28px 32px;
        }

        .receipt-store {
            text-align: center;
            margin-bottom: 20px;
        }

        .store-logo {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #E3F2FD;
        }

        .store-logo-fallback {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: linear-gradient(135deg, #1565C0, #42A5F5);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: #fff;
            font-size: 1.3rem;
        }

        .receipt-store h5 {
            font-size: 1rem;
            font-weight: 800;
            color: #1A2332;
            margin: 0 0 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .receipt-store p {
            font-size: .75rem;
            color: #78909C;
            margin: 0;
        }

        .receipt-divider {
            border: none;
            border-top: 1.5px dashed #CFD8DC;
            margin: 16px 0;
        }

        .receipt-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            font-size: .83rem;
        }

        .receipt-info-row .label {
            color: #78909C;
        }

        .receipt-info-row .value {
            font-weight: 700;
            color: #1A2332;
            text-align: right;
        }

        .receipt-items {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }

        .receipt-items thead th {
            font-size: .72rem;
            color: #90A4AE;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 6px 0;
            border-bottom: 1px solid #ECEFF1;
        }

        .receipt-items tbody td {
            padding: 9px 0;
            border-bottom: 1px solid #F5F7FA;
        }

        .receipt-items tbody tr:last-child td {
            border-bottom: none;
        }

        .item-name {
            font-size: .83rem;
            font-weight: 700;
            color: #1A2332;
        }

        .item-price {
            font-size: .75rem;
            color: #90A4AE;
        }

        .item-qty {
            font-size: .83rem;
            color: #546E7A;
            text-align: center;
        }

        .item-sub {
            font-size: .83rem;
            font-weight: 700;
            color: #1A2332;
            text-align: right;
        }

        .receipt-summary {
            background: #F8FAFD;
            border-radius: 10px;
            padding: 14px 16px;
        }

        .sum-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .83rem;
            padding: 4px 0;
        }

        .sum-row.total {
            font-size: 1rem;
            font-weight: 800;
            color: var(--rec-primary);
            padding-top: 10px;
            margin-top: 6px;
            border-top: 1.5px dashed #CFD8DC;
        }

        .sum-row .sum-label {
            color: #78909C;
        }

        .sum-row .sum-value {
            font-weight: 700;
            color: #1A2332;
        }

        .receipt-status {
            text-align: center;
            margin-top: 20px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 700;
        }

        .status-pill.completed {
            background: #E8F5E9;
            color: #2E7D32;
        }

        .status-pill.pending {
            background: #FFF8E1;
            color: #F57F17;
        }

        .status-pill.cancelled {
            background: #FFEBEE;
            color: #C62828;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1.5px dashed #CFD8DC;
        }

        .receipt-footer p {
            font-size: .78rem;
            color: #90A4AE;
            margin: 2px 0;
        }

        .receipt-footer .tagline {
            font-size: .72rem;
            color: #B0BEC5;
            margin-top: 6px;
        }

        /* Alert pending */
        .pending-notice {
            background: #FFF8E1;
            border: 1.5px solid #FFE082;
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .8rem;
            color: #F57F17;
            font-weight: 600;
        }

        @media print {

            body,
            .content-wrapper {
                background: #fff !important;
            }

            .sidebar,
            .navbar,
            .topbar,
            .receipt-topbar,
            .d-print-none {
                display: none !important;
            }

            .content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
            }

            .receipt-card {
                box-shadow: none !important;
            }

            @page {
                margin: .5cm;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="receipt-wrapper">

            {{-- ── Tentukan URL "back" berdasarkan ?from= parameter ──────── --}}
            @php
                $from = request('from', 'index'); // default ke riwayat transaksi
                $backUrl = match ($from) {
                    'pos' => route('dashboard.orders.pos'),
                    'confirmation' => route('dashboard.orders.confirmation'),
                    default => route('dashboard.orders.index'),
                };
                $backLabel = match ($from) {
                    'pos' => 'Kembali ke Kasir',
                    'confirmation' => 'Kembali ke Konfirmasi',
                    default => 'Kembali ke Riwayat',
                };
            @endphp

            {{-- Top Bar --}}
            <div class="receipt-topbar d-print-none">
                <a href="{{ $backUrl }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> {{ $backLabel }}
                </a>
                <button class="btn-print" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak Struk
                </button>
            </div>

            {{-- Receipt Card --}}
            <div class="receipt-card">
                <div class="receipt-stripe"></div>
                <div class="receipt-body">

                    {{-- Store Identity --}}
                    @php
                        $logo = \App\Models\SettingApp::get('app_image');
                        $logoPath = 'storage/' . $logo;
                        $hasLogo = $logo && file_exists(public_path($logoPath));
                        $storeName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
                        $storeAddr = \App\Models\SettingApp::get('store_address', '');
                        $storePhone = \App\Models\SettingApp::get('store_phone', '');
                    @endphp

                    <div class="receipt-store">
                        @if ($hasLogo)
                            <img src="{{ asset($logoPath) }}" alt="{{ $storeName }}" class="store-logo">
                        @else
                            <div class="store-logo-fallback"><i class="fas fa-paw"></i></div>
                        @endif
                        <h5>{{ $storeName }}</h5>
                        @if ($storeAddr)
                            <p>{{ $storeAddr }}</p>
                        @endif
                        @if ($storePhone)
                            <p><i class="fas fa-phone mr-1"></i>{{ $storePhone }}</p>
                        @endif
                    </div>

                    <hr class="receipt-divider">

                    {{-- Order Info --}}
                    <div class="receipt-info-row">
                        <span class="label">No. Invoice</span>
                        <span class="value" style="font-family:monospace; font-size:.82rem;">
                            {{ $order->invoice_number }}
                        </span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="label">Tanggal</span>
                        <span class="value">{{ $order->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="label">Kasir</span>
                        <span class="value">{{ $order->user->name }}</span>
                    </div>
                    <div class="receipt-info-row">
                        <span class="label">Metode Bayar</span>
                        <span class="value">{{ ucfirst($order->payment->payment_method ?? '-') }}</span>
                    </div>

                    <hr class="receipt-divider">

                    {{-- Items --}}
                    <table class="receipt-items">
                        <thead>
                            <tr>
                                <th style="width:50%">Produk</th>
                                <th style="width:10%;" class="text-center">Qty</th>
                                <th style="width:40%;" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="item-name">{{ $item->product->name ?? 'Produk dihapus' }}</div>
                                        <div class="item-price">Rp{{ number_format($item->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="item-qty">{{ $item->qty }}</td>
                                    <td class="item-sub">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr class="receipt-divider">

                    {{-- Summary --}}
                    <div class="receipt-summary">
                        <div class="sum-row">
                            <span class="sum-label">Subtotal</span>
                            <span class="sum-value">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->payment)
                            <div class="sum-row">
                                <span class="sum-label">Dibayar ({{ ucfirst($order->payment->payment_method) }})</span>
                                <span
                                    class="sum-value">Rp{{ number_format($order->payment->paid_amount ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="sum-row">
                                <span class="sum-label">Kembalian</span>
                                <span
                                    class="sum-value">Rp{{ number_format($order->payment->change_amount ?? 0, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="sum-row total">
                            <span>Total</span>
                            <span>Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="receipt-status">
                        <span class="status-pill {{ $order->status }}">
                            @if ($order->status === 'completed')
                                <i class="fas fa-check-circle"></i> Lunas
                            @elseif($order->status === 'pending')
                                <i class="fas fa-hourglass-half"></i> Menunggu Konfirmasi Admin
                            @else
                                <i class="fas fa-times-circle"></i> Dibatalkan
                            @endif
                        </span>
                    </div>

                    {{-- Notif tambahan jika pending --}}
                    @if ($order->status === 'pending')
                        <div class="pending-notice d-print-none">
                            <i class="fas fa-exclamation-circle" style="font-size:1.1rem; flex-shrink:0;"></i>
                            <span>Stok belum dipotong. Menunggu konfirmasi admin sebelum transaksi diproses.</span>
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="receipt-footer">
                        <p>Terima kasih telah mempercayakan</p>
                        <p>kebutuhan anabul Anda kepada kami! 🐾</p>
                        <p class="tagline">{{ $storeName }} — {{ now()->format('Y') }}</p>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const status = params.get('status');
            const invoice = params.get('invoice');

            // Notif sukses cash
            if (status === 'success' && invoice && invoice !== 'null') {
                Swal.fire({
                    icon: 'success',
                    title: 'Transaksi Berhasil!',
                    html: `No Invoice: <b>${invoice}</b>`,
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true,
                });
                // Bersihkan URL supaya tidak muncul lagi saat refresh
                window.history.replaceState({}, document.title, window.location.pathname +
                    '?from={{ request('from', 'index') }}');
            }
        });
    </script>
@endpush
