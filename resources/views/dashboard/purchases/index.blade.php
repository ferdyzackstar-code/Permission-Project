@extends('dashboard.layouts.admin')

@section('content')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('asset/css/purchases-style.css') }}">
    @endpush

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Manajemen Pembelian Barang</h2>
            <a href="{{ route('dashboard.purchases.confirmation') }}" class="btn btn-warning">
                <i class="fas fa-clock"></i> Konfirmasi Pembelian
                @if ($pendingPurchases > 0)
                    <span class="badge badge-light">{{ $pendingPurchases }}</span>
                @endif
            </a>
        </div>

        <!-- Info Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Produk</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Dalam Perjalanan
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingPurchases }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pembelian Selesai
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $receivedPurchases }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Pembelian Batal</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $cancelledPurchases }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Add/Edit Purchase -->
        <div class="card shadow mb-4" id="purchaseFormCard">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white" id="formTitle">
                    <i class="fas fa-plus-circle"></i> Tambah Pesanan Pembelian Baru
                </h6>
                <button type="button" class="btn btn-sm btn-secondary" id="resetFormBtn" style="display:none;">
                    <i class="fas fa-redo"></i> Reset Form
                </button>
            </div>
            <div class="card-body">
                <form id="purchaseForm">
                    @csrf
                    <input type="hidden" id="purchase_id" name="purchase_id">
                    <input type="hidden" id="form_method" value="POST">

                    <!-- Supplier Selection -->
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih Supplier <span class="text-danger">*</span></label>
                        <div class="row" id="supplierGrid">
                            @foreach ($suppliers as $supplier)
                                <div class="col-md-4 mb-3">
                                    <div class="supplier-card" data-supplier-id="{{ $supplier->id }}">
                                        <div class="card h-100 border-2 supplier-option">
                                            <div class="card-body text-center">
                                                <i class="fas fa-building fa-2x text-primary mb-2"></i>
                                                <h6 class="font-weight-bold mb-1">{{ $supplier->name }}</h6>
                                                <small class="text-muted">{{ $supplier->email }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="supplier_id" id="supplier_id" required>
                        <div class="invalid-feedback" id="supplierError">Pilih supplier terlebih dahulu!</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Tanggal Pembelian <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" id="purchase_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Catatan</label>
                                <input type="text" name="notes" id="notes" class="form-control"
                                    placeholder="Catatan tambahan (opsional)">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Product Items -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold mb-0">Detail Produk</h6>
                        <button type="button" class="btn btn-sm btn-success" id="addProductBtn">
                            <i class="fas fa-plus"></i> Tambah Produk
                        </button>
                    </div>

                    <div id="productItemsContainer">
                        <!-- Product items will be added here -->
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-right">
                            <h4 class="font-weight-bold text-primary">
                                Total Pembayaran: <span id="grandTotal">Rp 0</span>
                            </h4>
                        </div>
                    </div>

                    <hr>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="fas fa-save"></i> Buat Pesanan
                        </button>
                        <button class="btn btn-primary btn-loading d-none" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Menyimpan...
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Purchases -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-list"></i> Riwayat Pembelian
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="purchaseTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th width="3%" class="text-center">No</th>
                                <th>No PO</th>
                                <th>Tanggal</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th class="text-center">Status</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $index => $purchase)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><span class="badge badge-dark">{{ $purchase->purchase_number }}</span></td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->isoFormat('dddd, DD MMMM YYYY') }}
                                    </td>
                                    <td>{{ $purchase->supplier->name }}</td>
                                    <td class="font-weight-bold">Rp
                                        {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if ($purchase->status == 'received')
                                            <span class="badge badge-success text-white"><i class="fas fa-check"></i>
                                                Selesai</span>
                                        @elseif($purchase->status == 'cancelled')
                                            <span class="badge badge-danger text-white"><i class="fas fa-times"></i>
                                                Batal</span>
                                        @else
                                            <span class="badge badge-warning text-white"><i class="fas fa-clock"></i>
                                                Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center action-buttons">
                                        <button class="btn btn-sm btn-info detail-btn" data-id="{{ $purchase->id }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                        @if ($purchase->status == 'pending')
                                            <button class="btn btn-sm btn-warning edit-btn"
                                                data-id="{{ $purchase->id }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white" style="align-items: flex-start; padding: 15px;">
                    <div>
                        <h5 class="modal-title mb-0"><i class="fas fa-file-invoice"></i> Detail Pesanan Pembelian</h5>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal"
                        style="position: absolute; right: 15px; top: 15px;">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="detail-info">
                                <div class="detail-row">
                                    <label class="detail-label">No. PO</label>
                                    <span class="detail-value font-weight-bold" id="detail_po"></span>
                                </div>
                                <div class="detail-row">
                                    <label class="detail-label">Tanggal</label>
                                    <span class="detail-value" id="detail_date"></span>
                                </div>
                                <div class="detail-row">
                                    <label class="detail-label">Supplier</label>
                                    <span class="detail-value" id="detail_supplier"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-info">
                                <div class="detail-row">
                                    <label class="detail-label">Status</label>
                                    <span class="detail-value" id="detail_status"></span>
                                </div>
                                <div class="detail-row">
                                    <label class="detail-label">Catatan</label>
                                    <span class="detail-value" id="detail_notes"></span>
                                </div>
                                <div class="detail-row">
                                    <label class="detail-label">Total Pembayaran</label>
                                    <span class="detail-value font-weight-bold text-primary" id="detail_total"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-weight-bold mb-3 border-bottom pb-2">Detail Produk</h6>
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th>Produk</th>
                                <th width="15%">Harga Satuan</th>
                                <th width="10%">Qty</th>
                                <th width="15%">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detail_items"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .supplier-card {
            cursor: pointer;
            transition: all 0.3s;
        }

        .supplier-option {
            border: 2px solid #e3e6f0;
            transition: all 0.3s;
        }

        .supplier-option:hover {
            border-color: #4e73df;
            box-shadow: 0 0 10px rgba(78, 115, 223, 0.3);
            transform: translateY(-2px);
        }

        .supplier-option.selected {
            border-color: #4e73df;
            background-color: #f0f4ff;
        }

        .product-item-row {
            background: #f8f9fc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #e3e6f0;
        }

        .qty-control {
            display: flex;
            align-items: center;
        }

        .qty-control input {
            text-align: center;
            max-width: 80px;
            margin: 0 5px;
        }

        .qty-control button {
            width: 35px;
            height: 35px;
            padding: 0;
        }
    </style>

    <script>
        let productRowIndex = 0;
        const products = @json($products);

        // Format Rupiah Function
        function formatRupiah(angka) {
            if (angka === undefined || angka === null) return '0';
            let number = Math.round(parseFloat(angka));
            if (isNaN(number)) return '0';
            let number_string = number.toString();
            let sisa = number_string.length % 3;
            let rupiah = number_string.substr(0, sisa);
            let ribuan = number_string.substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah;
        }

        // Calculate Grand Total
        function calculateGrandTotal() {
            let total = 0;
            $('.product-item-row').each(function() {
                let qty = parseInt($(this).find('.qty-input').val()) || 0;
                let priceRaw = $(this).find('.price-input').val().replace(/\./g, '');
                let price = parseFloat(priceRaw) || 0;
                let subtotal = qty * price;
                $(this).find('.subtotal-display').text('Rp ' + formatRupiah(subtotal));
                total += subtotal;
            });
            $('#grandTotal').text('Rp ' + formatRupiah(total));
        }

        // Add Product Row
        function addProductRow(product_id = '', quantity = 1, price = '') {
            productRowIndex++;
            let productOptions = '<option value="">-- Pilih Produk --</option>';
            products.forEach(p => {
                let selected = p.id == product_id ? 'selected' : '';
                productOptions += `<option value="${p.id}" ${selected}>${p.name}</option>`;
            });

            let row = `
                <div class="product-item-row" data-index="${productRowIndex}">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="font-weight-bold">Produk</label>
                            <select name="product_id[]" class="form-control" required>
                                ${productOptions}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="font-weight-bold">Jumlah</label>
                            <div class="qty-control">
                                <button type="button" class="btn btn-sm btn-outline-secondary qty-minus">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity[]" class="form-control qty-input" 
                                    value="${quantity}" min="1" required>
                                <button type="button" class="btn btn-sm btn-outline-secondary qty-plus">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="font-weight-bold">Harga Satuan</label>
                            <input type="text" name="price[]" class="form-control price-input" 
                                value="${price}" placeholder="0" required>
                        </div>
                        <div class="col-md-2">
                            <label class="font-weight-bold">Subtotal</label>
                            <div class="form-control bg-light subtotal-display">Rp 0</div>
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-product">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            $('#productItemsContainer').append(row);
            calculateGrandTotal();
        }

        $(document).ready(function() {
            // DataTable
            $('#purchaseTable').DataTable({
                "order": [
                    [2, "desc"]
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                }
            });

            // Supplier Selection
            $(document).on('click', '.supplier-card', function() {
                $('.supplier-option').removeClass('selected');
                $(this).find('.supplier-option').addClass('selected');
                $('#supplier_id').val($(this).data('supplier-id'));
                $('#supplierError').hide();
            });

            // Add initial product row
            addProductRow();

            // Add Product Button
            $('#addProductBtn').on('click', function() {
                addProductRow();
            });

            // Remove Product Row
            $(document).on('click', '.remove-product', function() {
                if ($('.product-item-row').length > 1) {
                    $(this).closest('.product-item-row').remove();
                    calculateGrandTotal();
                } else {
                    Swal.fire('Perhatian!', 'Minimal harus ada 1 produk!', 'warning');
                }
            });

            // Quantity Plus/Minus
            $(document).on('click', '.qty-plus', function() {
                let input = $(this).siblings('.qty-input');
                let val = parseInt(input.val()) || 0;
                input.val(val + 1);
                calculateGrandTotal();
            });

            $(document).on('click', '.qty-minus', function() {
                let input = $(this).siblings('.qty-input');
                let val = parseInt(input.val()) || 0;
                if (val > 1) {
                    input.val(val - 1);
                    calculateGrandTotal();
                }
            });

            // Price Input Formatting - Fixed untuk max digit
            $(document).on('input', '.price-input', function() {
                let val = $(this).val();
                // Remove non-digit characters
                let digitsOnly = val.replace(/[^0-9]/g, '');
                // Limit to 15 digits (max Rp 999.999.999.999.999)
                if (digitsOnly.length > 15) {
                    digitsOnly = digitsOnly.substring(0, 15);
                }
                let formatted = formatRupiah(digitsOnly);
                $(this).val(formatted);
                let len = formatted.length;
                this.setSelectionRange(len, len);
                calculateGrandTotal();
            });

            // Quantity Change
            $(document).on('input change', '.qty-input', function() {
                calculateGrandTotal();
            });

            // Submit Form
            $('#purchaseForm').submit(function(e) {
                e.preventDefault();

                // Validate supplier
                if (!$('#supplier_id').val()) {
                    $('#supplierError').show();
                    Swal.fire('Error!', 'Pilih supplier terlebih dahulu!', 'error');
                    return;
                }

                $('.btn-submit').addClass('d-none');
                $('.btn-loading').removeClass('d-none');

                let method = $('#form_method').val();
                let url = method === 'PUT' ?
                    "{{ url('dashboard/purchases') }}/" + $('#purchase_id').val() :
                    "{{ route('dashboard.purchases.store') }}";

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $(this).serialize() + (method === 'PUT' ? '&_method=PUT' : ''),
                    success: function(res) {
                        Swal.fire('Berhasil!', res.message, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Gagal menyimpan data!';
                        Swal.fire('Error!', msg, 'error');
                        $('.btn-submit').removeClass('d-none');
                        $('.btn-loading').addClass('d-none');
                    }
                });
            });

            // Detail Button
            $(document).on('click', '.detail-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('#detail_po').text(data.purchase_number);

                    // Format date as: Hari, Tanggal Bulan Tahun
                    let dateObj = new Date(data.purchase_date);
                    let options = {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    let formattedDate = dateObj.toLocaleDateString('id-ID', options);
                    $('#detail_date').text(formattedDate);

                    $('#detail_supplier').text(data.supplier.name);
                    $('#detail_notes').text(data.notes || '-');
                    $('#detail_total').text('Rp ' + formatRupiah(data.total_amount));

                    let statusBadge = '';
                    if (data.status === 'received') {
                        statusBadge =
                            '<span class="badge badge-success"><i class="fas fa-check"></i> Selesai</span>';
                    } else if (data.status === 'cancelled') {
                        statusBadge =
                            '<span class="badge badge-danger"><i class="fas fa-times"></i> Batal</span>';
                    } else {
                        statusBadge =
                            '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>';
                    }
                    $('#detail_status').html(statusBadge);

                    let itemsHtml = '';
                    data.items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${item.product.name}</td>
                                <td>Rp ${formatRupiah(item.price)}</td>
                                <td>${item.quantity}</td>
                                <td class="font-weight-bold">Rp ${formatRupiah(item.subtotal)}</td>
                            </tr>
                        `;
                    });
                    $('#detail_items').html(itemsHtml);
                    $('#detailModal').modal('show');
                });
            });

            // Edit Button
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    // Scroll to form
                    $('html, body').animate({
                        scrollTop: $("#purchaseFormCard").offset().top - 100
                    }, 500);

                    // Change form mode
                    $('#formTitle').html('<i class="fas fa-edit"></i> Edit Pesanan Pembelian');
                    $('#form_method').val('PUT');
                    $('#purchase_id').val(data.id);
                    $('.btn-submit').html('<i class="fas fa-save"></i> Update Pesanan');
                    $('#resetFormBtn').show();

                    // Set supplier
                    $(`.supplier-card[data-supplier-id="${data.supplier_id}"]`).find(
                        '.supplier-option').addClass('selected');
                    $('#supplier_id').val(data.supplier_id);

                    // Set date and notes
                    $('#purchase_date').val(data.purchase_date);
                    $('#notes').val(data.notes);

                    // Clear and add product rows
                    $('#productItemsContainer').empty();
                    data.items.forEach(item => {
                        addProductRow(item.product_id, item.quantity, formatRupiah(item
                            .price));
                    });

                    calculateGrandTotal();
                });
            });

            // Reset Form Button
            $('#resetFormBtn').on('click', function() {
                $('#formTitle').html('<i class="fas fa-plus-circle"></i> Tambah Pesanan Pembelian Baru');
                $('#form_method').val('POST');
                $('#purchase_id').val('');
                $('#purchaseForm')[0].reset();
                $('.supplier-option').removeClass('selected');
                $('#supplier_id').val('');
                $('.btn-submit').html('<i class="fas fa-save"></i> Buat Pesanan');
                $(this).hide();

                $('#productItemsContainer').empty();
                addProductRow();
                calculateGrandTotal();
            });
        });
    </script>
@endpush
