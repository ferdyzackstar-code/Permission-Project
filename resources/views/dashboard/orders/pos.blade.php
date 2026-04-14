@extends('dashboard.layouts.admin')

@section('title', 'Kasir - Smart Toko')

@push('styles')
    <link rel="stylesheet" href="{{ asset('asset/css/pos-style.css') }}">
@endpush

@section('content')

    <div class="container-fluid mt-3 pos-main-wrapper">
        <div class="row h-100">
            <div class="col-md-8 h-100">
                <div class="card shadow-sm border-1 mb-3 h-100">
                    <div class="card-body d-flex flex-column">

                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                            <div class="search-box mx-2 w-100">
                                <input type="text" id="product-search" class="form-control" placeholder="Cari produk...">
                            </div>
                        </div>

                        <div class="product-scroll-area">
                            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4" id="product-list">
                                @foreach ($products as $product)
                                    <div class="col product-item mb-3" data-category="{{ $product->category_id }}"
                                        data-name="{{ strtolower($product->name) }}"
                                        data-species="{{ strtolower($product->species ?? '') }}">

                                        <div class="card h-100 card-product shadow-sm border-1"
                                            onclick="addToCart({{ json_encode($product) }})">
                                            <div class="img-wrapper p-2">
                                                <img src="{{ asset('storage/uploads/products/' . ($product->image ?? 'default-product.jpg')) }}"
                                                    class="card-img-top rounded" alt="{{ $product->name }}"
                                                    style="height: 140px; object-fit: cover;">
                                            </div>
                                            <div class="card-body p-2 d-flex flex-column justify-content-between">
                                                <div>
                                                    <p class="card-title mb-1 small text-dark fw-bold text-truncate">
                                                        {{ $product->name }}</p>
                                                    <p class="text-primary fw-bold mb-0 small">
                                                        Rp{{ number_format($product->price, 0, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">Stok: {{ $product->stock }}</small>
                                                    <i class="fas fa-plus-circle text-success"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="sticky-cart-wrapper">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center py-3">
                            <span><i class="fas fa-shopping-cart me-2 text-primary"></i> Keranjang</span>
                            <span class="badge bg-soft-primary text-primary" id="cart-count">0 Item</span>
                        </div>

                        <div class="card-body p-0">
                            <div class="cart-items-box" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <tbody id="cart-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer bg-light border-0 p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold fs-5">Total:</span>
                                <span id="total-display" class="fw-bold text-primary fs-5">Rp0</span>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold mb-1">Metode Bayar:</label>
                                <select id="payment_method" class="form-select">
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>

                            <div id="cash-input-group">
                                <div class="mb-2">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text fw-bold bg-white">Rp</span>
                                        <input type="text" id="paid_amount_format" class="form-control fw-bold"
                                            placeholder="0">
                                        <input type="hidden" id="paid_amount" value="0">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mb-3 px-1">
                                    <span class="text-muted small">Kembalian:</span>
                                    <span id="change_amount" class="fw-bold text-dark">Rp0</span>
                                </div>
                            </div>

                            <button type="button" class="btn btn-danger btn-sm w-100 mb-2" onclick="clearCart()">
                                <i class="fa fa-trash"></i> Kosongkan Keranjang
                            </button>

                            <button id="btn-submit" class="btn btn-primary w-100 fw-bold py-3 shadow-sm border-0"
                                onclick="submitTransaction()">
                                PROSES TRANSAKSI
                            </button>

                        </div>
                    </div>
                </div>
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
    </script>
    <script src="{{ asset('asset/js/pos-logic.js') }}"></script>
@endpush
