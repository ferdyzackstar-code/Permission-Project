@extends('dashboard.layouts.admin')

@section('title', 'Kasir - Smart Toko')

@push('styles')
    <style>
        .product-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .cart-container {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 150px);
            display: flex;
            flex-direction: column;
        }

        .cart-items {
            overflow-y: auto;
            flex-grow: 1;
        }

        .category-badge {
            cursor: pointer;
            transition: 0.3s;
        }

        .category-badge.active {
            background-color: #007bff !important;
            color: white;
        }

        .img-product {
            height: 120px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 font-weight-bold">Daftar Produk</h5>
                        <div class="input-group w-50">
                            <input type="text" id="search-product" class="form-control form-control-sm"
                                placeholder="Cari nama produk...">
                        </div>
                    </div>
                    <div class="categories mb-3">
                        <span class="badge badge-light p-2 mr-2 category-badge active" data-id="all">Semua</span>
                        @foreach ($categories as $cat)
                            <span class="badge badge-light p-2 mr-2 category-badge"
                                data-id="{{ $cat->id }}">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row" id="product-grid">
                @foreach ($products as $product)
                    <div class="col-md-4 col-6 mb-4 product-item" data-category="{{ $product->category_id }}"
                        data-name="{{ strtolower($product->name) }}">
                        <div class="card product-card h-100" onclick="addToCart({{ json_encode($product) }})">
                            <img src="{{ $product->image ? asset('storage/uploads/products/' . $product->image) : asset('images/no-image.png') }}"
                                class="card-img-top img-product" alt="...">
                            <div class="card-body p-2">
                                <small class="text-muted d-block">{{ $product->category->name ?? 'Uncategorized' }}</small>
                                <h6 class="card-title mb-1 font-weight-bold">{{ $product->name }}</h6>
                                <p class="text-primary mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <small class="text-{{ $product->stock < 10 ? 'danger' : 'muted' }}">Stok:
                                    {{ $product->stock }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card cart-container border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold"><i class="fa fa-shopping-cart mr-2"></i>Keranjang</h5>
                </div>
                <div class="card-body cart-items p-2">
                    <div id="empty-cart" class="text-center py-5">
                        <i class="fa fa-shopping-basket fa-3x text-light mb-3"></i>
                        <p class="text-muted">Keranjang masih kosong</p>
                    </div>
                    <ul class="list-group list-group-flush" id="cart-list">
                    </ul>
                </div>
                <div class="card-footer bg-white border-0">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span id="total-amount" class="font-weight-bold">Rp 0</span>
                    </div>
                    <button class="btn btn-primary btn-block py-2 font-weight-bold" id="btn-checkout" disabled
                        data-toggle="modal" data-target="#modalCheckout">
                        CHECKOUT
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCheckout" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-checkout">
                    <div class="modal-body">
                        <h3 class="text-center mb-4 font-weight-bold text-primary" id="modal-total">Rp 0</h3>
                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <select class="form-control" name="payment_method" id="payment_method" required>
                                <option value="cash">Tunai (Cash)</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                        <div id="cash-input-group">
                            <div class="form-group">
                                <label>Uang Dibayar</label>
                                <input type="text" class="form-control input-rupiah" name="paid_amount" id="paid_amount">
                            </div>
                            <div class="form-group">
                                <label>Kembalian</label>
                                <input type="text" class="form-control" id="change_amount" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Catatan (Opsional)</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-order">Selesaikan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let cart = [];
        let total = 0;

        // Tambah ke Keranjang
        function addToCart(product) {
            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                if (existing.qty < product.stock) {
                    existing.qty++;
                } else {
                    Swal.fire('Ups!', 'Stok tidak mencukupi', 'warning');
                    return;
                }
            } else {
                cart.push({
                    ...product,
                    qty: 1
                });
            }
            renderCart();
        }

        // Update Quantity
        function updateQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.qty += delta;
                if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
                renderCart();
            }
        }

        // Render Tampilan Keranjang
        function renderCart() {
            const cartList = $('#cart-list');
            const emptyCart = $('#empty-cart');
            cartList.empty();
            total = 0;

            if (cart.length === 0) {
                emptyCart.show();
                $('#btn-checkout').prop('disabled', true);
            } else {
                emptyCart.hide();
                $('#btn-checkout').prop('disabled', false);

                cart.forEach(item => {
                    const subtotal = item.price * item.qty;
                    total += subtotal;
                    cartList.append(`
                    <li class="list-group-item px-0 border-0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="width: 60%">
                                <h6 class="mb-0 font-weight-bold text-truncate">${item.name}</h6>
                                <small class="text-muted">Rp ${formatNumber(item.price)}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-secondary px-2" onclick="updateQty(${item.id}, -1)">-</button>
                                <span class="mx-2 font-weight-bold">${item.qty}</span>
                                <button class="btn btn-sm btn-outline-secondary px-2" onclick="updateQty(${item.id}, 1)">+</button>
                            </div>
                        </div>
                    </li>
                `);
                });
            }
            $('#total-amount').text('Rp ' + formatNumber(total));
            $('#modal-total').text('Rp ' + formatNumber(total));
        }

        // Format Rupiah Number
        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        // Pencarian & Filter Kategori
        $('#search-product').on('keyup', function() {
            const val = $(this).val().toLowerCase();
            $('.product-item').each(function() {
                $(this).toggle($(this).data('name').includes(val));
            });
        });

        $('.category-badge').on('click', function() {
            $('.category-badge').removeClass('active btn-primary').addClass('badge-light');
            $(this).addClass('active btn-primary').removeClass('badge-light');
            const catId = $(this).data('id');
            $('.product-item').each(function() {
                if (catId === 'all') $(this).show();
                else $(this).toggle($(this).data('category') == catId);
            });
        });

        // Handle Modal Checkout & Kembalian
        $('#paid_amount').on('keyup', function() {
            const paid = parseInt($(this).val().replace(/\./g, '')) || 0;
            const kembalian = paid - total;
            $('#change_amount').val(kembalian >= 0 ? 'Rp ' + formatNumber(kembalian) : 'Rp 0');
        });

        // Submit Transaksi via AJAX
        $('#form-checkout').on('submit', function(e) {
            e.preventDefault();
            const paidAmount = parseInt($('#paid_amount').val().replace(/\./g, '')) || 0;

            if ($('#payment_method').val() === 'cash' && paidAmount < total) {
                Swal.fire('Gagal', 'Uang yang dibayar kurang!', 'error');
                return;
            }

            $.ajax({
                url: "{{ route('dashboard.orders.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    cart: cart,
                    total_amount: total,
                    payment_method: $('#payment_method').val(),
                    paid_amount: paidAmount,
                    notes: $('textarea[name=notes]').val()
                },
                success: function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => {
                        location.reload(); // Atau redirect ke struk
                    });
                },
                error: function(err) {
                    Swal.fire('Error', err.responseJSON.message, 'error');
                }
            });
        });
    </script>
@endpush
