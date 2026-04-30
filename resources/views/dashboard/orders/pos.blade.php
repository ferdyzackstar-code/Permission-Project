@extends('dashboard.layouts.admin')

@section('title', 'Point of Sales — Anda Petshop')

@push('styles')
    <style>
        /* ── ROOT VARIABLES ─────────────────────────────────────────── */
        :root {
            --pos-primary: #1565C0;
            --pos-primary-lt: #1976D2;
            --pos-accent: #42A5F5;
            --pos-success: #2E7D32;
            --pos-danger: #C62828;
            --pos-warning: #F57F17;
            --pos-bg: #F0F4F8;
            --pos-card: #FFFFFF;
            --pos-border: #E3EAF2;
            --pos-text: #1A2332;
            --pos-muted: #7B8FA6;
            --pos-radius: 14px;
            --pos-radius-sm: 8px;
            --cart-height: calc(100vh - 280px);
        }

        /* ── LAYOUT ─────────────────────────────────────────────────── */
        body {
            background: var(--pos-bg);
        }

        .pos-wrapper {
            display: flex;
            gap: 20px;
            height: calc(100vh - 140px);
            padding: 0 8px 8px;
        }

        /* ── LEFT PANEL: PRODUK ─────────────────────────────────────── */
        .pos-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--pos-card);
            border-radius: var(--pos-radius);
            box-shadow: 0 2px 16px rgba(21, 101, 192, .08);
            overflow: hidden;
        }

        .pos-left-header {
            padding: 16px 20px 12px;
            border-bottom: 1px solid var(--pos-border);
            background: var(--pos-card);
            flex-shrink: 0;
        }

        .pos-left-header h5 {
            font-size: .95rem;
            font-weight: 700;
            color: var(--pos-text);
            margin: 0 0 12px;
        }

        /* Search */
        .pos-search-wrap {
            position: relative;
        }

        .pos-search-wrap .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--pos-muted);
            font-size: .85rem;
            pointer-events: none;
        }

        .pos-search-wrap input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1.5px solid var(--pos-border);
            border-radius: var(--pos-radius-sm);
            font-size: .88rem;
            color: var(--pos-text);
            background: #F8FAFD;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        .pos-search-wrap input:focus {
            border-color: var(--pos-accent);
            box-shadow: 0 0 0 3px rgba(66, 165, 245, .15);
            background: #fff;
        }

        /* Product Grid */
        .pos-product-grid {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 14px;
            align-content: start;
        }

        .pos-product-grid::-webkit-scrollbar {
            width: 5px;
        }

        .pos-product-grid::-webkit-scrollbar-track {
            background: transparent;
        }

        .pos-product-grid::-webkit-scrollbar-thumb {
            background: #CBD5E0;
            border-radius: 10px;
        }

        /* Product Card */
        .product-card {
            background: var(--pos-card);
            border: 1.5px solid var(--pos-border);
            border-radius: var(--pos-radius);
            cursor: pointer;
            transition: all .22s ease;
            overflow: hidden;
            position: relative;
            user-select: none;
        }

        .product-card:hover {
            border-color: var(--pos-primary-lt);
            box-shadow: 0 6px 20px rgba(21, 101, 192, .14);
            transform: translateY(-3px);
        }

        .product-card:active {
            transform: scale(.97);
        }

        .product-card .pc-img-wrap {
            background: #F0F4F8;
            overflow: hidden;
            aspect-ratio: 1;
        }

        .product-card .pc-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .3s ease;
        }

        .product-card:hover .pc-img-wrap img {
            transform: scale(1.07);
        }

        .product-card .pc-body {
            padding: 10px 10px 8px;
        }

        .product-card .pc-name {
            font-size: .8rem;
            font-weight: 700;
            color: var(--pos-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }

        .product-card .pc-price {
            font-size: .82rem;
            font-weight: 800;
            color: var(--pos-primary);
        }

        .product-card .pc-stock {
            font-size: .72rem;
            color: var(--pos-muted);
            margin-top: 4px;
        }

        .product-card .pc-add-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            background: var(--pos-primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .72rem;
            opacity: 0;
            transform: scale(.8);
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(21, 101, 192, .3);
        }

        .product-card:hover .pc-add-btn {
            opacity: 1;
            transform: scale(1);
        }

        /* Out of stock overlay */
        .product-card.out-of-stock {
            opacity: .55;
            cursor: not-allowed;
            pointer-events: none;
        }

        .product-card.out-of-stock::after {
            content: 'Habis';
            position: absolute;
            top: 8px;
            left: 8px;
            background: var(--pos-danger);
            color: #fff;
            font-size: .65rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        /* Empty search state */
        .pos-empty {
            grid-column: 1/-1;
            text-align: center;
            padding: 40px 20px;
            color: var(--pos-muted);
        }

        .pos-empty i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 10px;
            opacity: .4;
        }

        /* ── RIGHT PANEL: CART ──────────────────────────────────────── */
        .pos-right {
            width: 340px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            background: var(--pos-card);
            border-radius: var(--pos-radius);
            box-shadow: 0 2px 16px rgba(21, 101, 192, .08);
            overflow: hidden;
        }

        /* Cart Header */
        .cart-header {
            background: linear-gradient(135deg, var(--pos-primary) 0%, var(--pos-primary-lt) 100%);
            padding: 16px 20px;
            color: #fff;
            flex-shrink: 0;
        }

        .cart-header .cart-title {
            font-size: .95rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-header .cart-badge {
            background: rgba(255, 255, 255, .22);
            border-radius: 20px;
            padding: 2px 10px;
            font-size: .75rem;
            font-weight: 700;
            backdrop-filter: blur(4px);
        }

        /* Cart Items */
        .cart-items {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }

        .cart-items::-webkit-scrollbar {
            width: 4px;
        }

        .cart-items::-webkit-scrollbar-thumb {
            background: #CBD5E0;
            border-radius: 10px;
        }

        .cart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 160px;
            color: var(--pos-muted);
            font-size: .85rem;
            gap: 8px;
        }

        .cart-empty i {
            font-size: 2rem;
            opacity: .35;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-bottom: 1px solid #f3f5f8;
            transition: background .15s;
        }

        .cart-item:hover {
            background: #F8FAFD;
        }

        .cart-item-img {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--pos-border);
            flex-shrink: 0;
        }

        .cart-item-info {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-size: .8rem;
            font-weight: 700;
            color: var(--pos-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cart-item-price {
            font-size: .75rem;
            color: var(--pos-muted);
        }

        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .qty-btn {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            border: 1.5px solid var(--pos-border);
            background: #F0F4F8;
            color: var(--pos-text);
            font-size: .78rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .15s;
            line-height: 1;
            padding: 0;
        }

        .qty-btn:hover {
            background: var(--pos-primary);
            color: #fff;
            border-color: var(--pos-primary);
        }

        .qty-btn.minus:hover {
            background: #EF5350;
            border-color: #EF5350;
        }

        .qty-num {
            font-size: .85rem;
            font-weight: 800;
            color: var(--pos-text);
            min-width: 22px;
            text-align: center;
        }

        .cart-item-subtotal {
            font-size: .8rem;
            font-weight: 800;
            color: var(--pos-primary);
            min-width: 70px;
            text-align: right;
            flex-shrink: 0;
        }

        /* Cart Footer */
        .cart-footer {
            padding: 14px 16px;
            border-top: 1.5px solid var(--pos-border);
            background: #FAFBFD;
            flex-shrink: 0;
        }

        .cart-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 10px 14px;
            background: linear-gradient(135deg, #E3F2FD, #EDE7F6);
            border-radius: var(--pos-radius-sm);
        }

        .cart-total-row .label {
            font-size: .88rem;
            font-weight: 700;
            color: var(--pos-text);
        }

        .cart-total-row .value {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--pos-primary);
        }

        /* Payment Method */
        .pay-method-row {
            margin-bottom: 10px;
        }

        .pay-method-row label {
            font-size: .78rem;
            font-weight: 700;
            color: var(--pos-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 6px;
            display: block;
        }

        .pay-method-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }

        .pay-tab {
            padding: 8px 10px;
            border: 1.5px solid var(--pos-border);
            border-radius: var(--pos-radius-sm);
            background: #F8FAFD;
            color: var(--pos-muted);
            font-size: .8rem;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            user-select: none;
        }

        .pay-tab:hover {
            border-color: var(--pos-primary);
            color: var(--pos-primary);
        }

        .pay-tab.active {
            background: var(--pos-primary);
            border-color: var(--pos-primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(21, 101, 192, .25);
        }

        /* Hidden real select */
        #payment_method {
            display: none;
        }

        /* Cash Input */
        .cash-input-section {
            margin-bottom: 10px;
        }

        .cash-input-section label {
            font-size: .78rem;
            font-weight: 700;
            color: var(--pos-muted);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 6px;
            display: block;
        }

        .cash-input-wrap {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--pos-border);
            border-radius: var(--pos-radius-sm);
            overflow: hidden;
            transition: border-color .2s;
            background: #fff;
        }

        .cash-input-wrap:focus-within {
            border-color: var(--pos-accent);
        }

        .cash-input-prefix {
            padding: 0 12px;
            font-size: .85rem;
            font-weight: 700;
            color: var(--pos-muted);
            background: #F0F4F8;
            height: 42px;
            display: flex;
            align-items: center;
            border-right: 1px solid var(--pos-border);
        }

        #paid_amount_format {
            flex: 1;
            border: none;
            outline: none;
            padding: 0 12px;
            font-size: 1rem;
            font-weight: 700;
            color: var(--pos-text);
            height: 42px;
            background: transparent;
        }

        .change-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            margin-bottom: 8px;
        }

        .change-row .change-label {
            font-size: .78rem;
            color: var(--pos-muted);
        }

        .change-row .change-value {
            font-size: .9rem;
            font-weight: 800;
            color: var(--pos-success);
        }

        /* Buttons */
        .btn-clear-cart {
            width: 100%;
            padding: 8px;
            border-radius: var(--pos-radius-sm);
            border: 1.5px solid #FFCDD2;
            background: #FFF5F5;
            color: var(--pos-danger);
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-clear-cart:hover {
            background: #FFEBEE;
            border-color: var(--pos-danger);
        }

        .btn-checkout {
            width: 100%;
            padding: 13px;
            border-radius: var(--pos-radius-sm);
            border: none;
            background: linear-gradient(135deg, var(--pos-primary) 0%, var(--pos-primary-lt) 100%);
            color: #fff;
            font-size: .95rem;
            font-weight: 800;
            cursor: pointer;
            transition: all .2s;
            letter-spacing: .5px;
            box-shadow: 0 4px 14px rgba(21, 101, 192, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-checkout:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(21, 101, 192, .4);
        }

        .btn-checkout:active {
            transform: scale(.98);
        }

        .btn-checkout:disabled {
            background: #B0BEC5;
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }

        /* ── BADGE ANIMATION saat produk ditambah ───────────────────── */
        @keyframes pop {

            0%,
            100% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.3)
            }
        }

        .cart-badge.pop {
            animation: pop .25s ease;
        }

        /* ── RESPONSIVE ─────────────────────────────────────────────── */
        @media (max-width: 991px) {
            .pos-wrapper {
                flex-direction: column;
                height: auto;
                overflow: auto;
            }

            .pos-right {
                width: 100%;
                max-height: 600px;
            }

            .pos-product-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                max-height: 55vh;
            }
        }

        @media (max-width: 575px) {
            .pos-product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                padding: 12px;
            }

            .pos-wrapper {
                padding: 0 4px 8px;
                gap: 12px;
            }

            .cart-footer {
                padding: 12px;
            }

            .pay-method-tabs {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="pos-wrapper">

        {{-- ── LEFT: PRODUK ─────────────────────────────────────────── --}}
        <div class="pos-left">
            <div class="pos-left-header">
                <h5><i class="fas fa-store mr-2 text-primary"></i>Pilih Produk</h5>
                <div class="pos-search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="product-search" placeholder="Cari nama produk...">
                </div>
            </div>

            <div class="pos-product-grid" id="product-list">
                @forelse($products as $product)
                    <div class="product-card {{ $product->stock <= 0 ? 'out-of-stock' : '' }} product-item"
                        data-name="{{ strtolower($product->name) }}" data-category="{{ $product->category_id }}"
                        onclick="addToCart({{ json_encode($product) }})">

                        <div class="pc-img-wrap">
                            <img src="{{ asset('storage/uploads/products/' . ($product->image ?? 'default-product.jpg')) }}"
                                alt="{{ $product->name }}" loading="lazy"
                                onerror="this.src='{{ asset('storage/uploads/products/default-product.jpg') }}'">
                        </div>

                        <div class="pc-body">
                            <div class="pc-name" title="{{ $product->name }}">{{ $product->name }}</div>
                            <div class="pc-price">Rp{{ number_format($product->price, 0, ',', '.') }}</div>
                            <div class="pc-stock">
                                <i class="fas fa-box-open mr-1" style="font-size:.65rem;"></i>
                                Stok: {{ $product->stock }}
                            </div>
                        </div>

                        <div class="pc-add-btn">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                @empty
                    <div class="pos-empty">
                        <i class="fas fa-box-open"></i>
                        Belum ada produk tersedia
                    </div>
                @endforelse

                {{-- Empty search state (hidden by default) --}}
                <div class="pos-empty" id="search-empty" style="display:none;">
                    <i class="fas fa-search"></i>
                    Produk tidak ditemukan
                </div>
            </div>
        </div>

        {{-- ── RIGHT: CART ──────────────────────────────────────────── --}}
        <div class="pos-right">

            {{-- Cart Header --}}
            <div class="cart-header">
                <p class="cart-title">
                    <i class="fas fa-shopping-cart"></i>
                    Keranjang
                    <span class="cart-badge ml-auto" id="cart-count">0 Item</span>
                </p>
            </div>

            {{-- Cart Items --}}
            <div class="cart-items" id="cart-items-wrap">
                <div class="cart-empty" id="cart-empty-state">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Keranjang masih kosong</span>
                    <small style="font-size:.75rem; opacity:.7;">Klik produk untuk menambahkan</small>
                </div>
                <div id="cart-table-body"></div>
            </div>

            {{-- Cart Footer --}}
            <div class="cart-footer">

                {{-- Total --}}
                <div class="cart-total-row">
                    <span class="label"><i class="fas fa-receipt mr-1"></i>Total</span>
                    <span class="value" id="total-display">Rp0</span>
                </div>

                {{-- Metode Bayar --}}
                <div class="pay-method-row">
                    <label>Metode Pembayaran</label>
                    <div class="pay-method-tabs">
                        <div class="pay-tab active" data-value="cash" onclick="selectPayMethod('cash')">
                            <i class="fas fa-money-bill-wave"></i> Tunai
                        </div>
                        <div class="pay-tab" data-value="transfer" onclick="selectPayMethod('transfer')">
                            <i class="fas fa-university"></i> Transfer
                        </div>
                    </div>
                    <select id="payment_method">
                        <option value="cash">cash</option>
                        <option value="transfer">transfer</option>
                    </select>
                </div>

                {{-- Cash Input (hidden jika transfer) --}}
                <div class="cash-input-section" id="cash-input-group">
                    <label>Uang Diterima</label>
                    <div class="cash-input-wrap">
                        <div class="cash-input-prefix">Rp</div>
                        <input type="text" id="paid_amount_format" placeholder="0" autocomplete="off">
                        <input type="hidden" id="paid_amount" value="0">
                    </div>
                    <div class="change-row">
                        <span class="change-label"><i class="fas fa-coins mr-1"></i>Kembalian</span>
                        <span class="change-value" id="change_amount">Rp0</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <button class="btn-clear-cart" onclick="clearCart()">
                    <i class="fas fa-trash-alt"></i> Kosongkan Keranjang
                </button>

                <button class="btn-checkout" id="btn-submit" onclick="submitTransaction()">
                    <i class="fas fa-check-circle"></i>
                    PROSES TRANSAKSI
                </button>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.posConfig = {
            storeUrl: "{{ route('dashboard.orders.store') }}",
            csrfToken: "{{ csrf_token() }}",
            assetUrl: "{{ asset('storage/uploads/products/') }}"
        };

        // ── Payment method tab switcher ──────────────────────────────
        function selectPayMethod(val) {
            document.getElementById('payment_method').value = val;
            document.querySelectorAll('.pay-tab').forEach(t => {
                t.classList.toggle('active', t.dataset.value === val);
            });
            const cashGroup = document.getElementById('cash-input-group');
            if (val === 'transfer') {
                cashGroup.style.display = 'none';
                document.getElementById('paid_amount').value = 0;
            } else {
                cashGroup.style.display = 'block';
            }
            calculateChange();
        }
    </script>
    <script src="{{ asset('asset/js/pos-logic.js') }}"></script>
@endpush
