@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Transaksi Pembelian</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus"></i> Tambah Pembelian
            </button>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchases as $p)
                            <tr>
                                <td>{{ $p->purchase_number }}</td>
                                <td>{{ date('d/m/Y', strtotime($p->purchase_date)) }}</td>
                                <td>{{ $p->supplier->name }}</td>
                                <td>Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info show-btn"
                                        data-id="{{ $p->id }}">Detail</button>
                                    <button class="btn btn-sm btn-danger delete-btn"
                                        data-id="{{ $p->id }}">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <form id="createForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah Pembelian Barang</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Supplier</label>
                                <select name="supplier_id" class="form-control select2" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Tanggal Pembelian</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label>Catatan</label>
                                <input type="text" name="notes" class="form-control" placeholder="Opsional">
                            </div>
                        </div>

                        <hr>
                        <h6 class="font-weight-bold mb-3">Detail Barang <button type="button"
                                class="btn btn-sm btn-success float-right" id="addButton">+ Tambah Baris</button></h6>

                        <div id="newEntriesContainer">
                            <div class="row entry mb-2 align-items-end">
                                <div class="col-md-4">
                                    <label>Produk</label>
                                    <select name="product_id[]" class="form-control select2" required>
                                        <option value="" disabled selected>Pilih Produk</option>
                                        @foreach ($products as $prod)
                                            <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Qty</label>
                                    <input type="number" name="quantity[]" class="form-control qty-input" min="1"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label>Harga Satuan</label>
                                    <input type="text" name="price[]" class="form-control price-input" required>
                                </div>
                                <div class="col-md-3">
                                    <label>Subtotal</label>
                                    <input type="text" class="form-control subtotal-input bg-light" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-right">
                                <h4 class="font-weight-bold text-primary">Grand Total: <span id="grandTotalDisplay">Rp
                                        0</span></h4>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="showModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detail Transaksi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless table-sm mb-4">
                        <tr>
                            <th width="20%">No. PO</th>
                            <td id="det_po"></td>
                            <th width="20%">Tanggal</th>
                            <td id="det_date"></td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td id="det_sup"></td>
                            <th>Catatan</th>
                            <td id="det_notes"></td>
                        </tr>
                    </table>
                    <table class="table table-striped">
                        <thead class="bg-light">
                            <tr>
                                <th>Barang</th>
                                <th>Harga Satuan</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            // Fungsi Format Rupiah
            function formatRupiah(angka) {
                var number_string = angka.toString().replace(/[^,\d]/g, ''),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return rupiah;
            }

            // Fungsi Hitung Subtotal & Grand Total
            function calculateTotals() {
                let grandTotal = 0;
                $('.entry').each(function() {
                    let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                    let priceRaw = $(this).find('.price-input').val().replace(/\./g, '');
                    let price = parseFloat(priceRaw) || 0;

                    let subtotal = qty * price;
                    $(this).find('.subtotal-input').val(formatRupiah(subtotal));
                    grandTotal += subtotal;
                });
                $('#grandTotalDisplay').text('Rp ' + formatRupiah(grandTotal));
            }

            // Event Listener untuk perhitungan dinamis
            $(document).on('keyup change', '.qty-input, .price-input', function() {
                if ($(this).hasClass('price-input')) {
                    $(this).val(formatRupiah($(this).val()));
                }
                calculateTotals();
            });

            // Tambah Baris Baru
            $('#addButton').on('click', function() {
                var row = `
        <div class="row entry mb-2 align-items-end">
            <div class="col-md-4">
                <select name="product_id[]" class="form-control select2-new" required>
                    <option value="" disabled selected>Pilih Produk</option>
                    @foreach ($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="quantity[]" class="form-control qty-input" min="1" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="price[]" class="form-control price-input" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control subtotal-input bg-light" readonly>
            </div>
            <div class="col-md-1 text-right">
                <button type="button" class="btn btn-danger deleteEntry"><i class="fas fa-trash"></i></button>
            </div>
        </div>`;

                $('#newEntriesContainer').append(row);
                $('.select2-new').select2({
                    width: '100%'
                }).removeClass('select2-new');
            });

            // Hapus Baris
            $(document).on('click', '.deleteEntry', function() {
                $(this).closest('.entry').remove();
                calculateTotals();
            });

            // Submit Form Transaksi
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('dashboard.purchases.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        alert(res.message);
                        location.reload(); // Reload untuk memperbarui tabel utama
                    },
                    error: function(err) {
                        alert("Gagal menyimpan transaksi!");
                    }
                });
            });

            // Tampilkan Detail
            $(document).on('click', '.show-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('#det_po').text(data.purchase_number);
                    $('#det_date').text(data.purchase_date);
                    $('#det_sup').text(data.supplier.name);
                    $('#det_notes').text(data.notes || '-');

                    let itemsHtml = '';
                    data.items.forEach(function(item) {
                        itemsHtml += `
                    <tr>
                        <td>${item.product.name}</td>
                        <td>Rp ${formatRupiah(item.price)}</td>
                        <td>${item.quantity}</td>
                        <td>Rp ${formatRupiah(item.subtotal)}</td>
                    </tr>`;
                    });
                    $('#detailItemsTable').html(itemsHtml);
                    $('#showModal').modal('show');
                });
            });

            // Hapus Transaksi (Plus cascade item & kembalikan stok)
            $(document).on('click', '.delete-btn', function() {
                if (confirm("Hapus transaksi ini? Stok produk akan dikembalikan secara otomatis.")) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: "{{ url('dashboard/purchases') }}/" + id,
                        type: "POST",
                        data: {
                            _method: "DELETE",
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            alert(res.message);
                            location.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush
