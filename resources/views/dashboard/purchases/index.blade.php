@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Transaksi Pembelian Barang</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                <i class="fas fa-plus"></i> Tambah Pembelian
            </button>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="purchaseTable">
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
                                    <td><span class="badge badge-dark">{{ $p->purchase_number }}</span></td>
                                    <td>{{ date('d/m/Y', strtotime($p->purchase_date)) }}</td>
                                    <td>{{ $p->supplier->name }}</td>
                                    <td class="font-weight-bold">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info show-btn"
                                            data-id="{{ $p->id }}">Detail</button>
                                        <button class="btn btn-sm btn-warning edit-btn"
                                            data-id="{{ $p->id }}">Edit</button>
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
                        <h6 class="font-weight-bold">Detail Barang <button type="button"
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
                                <div class="col-md-2">
                                    <label>Subtotal</label>
                                    <input type="text" class="form-control subtotal-input bg-light" readonly>
                                </div>
                                <div class="col-md-1 text-right">
                                    <button type="button" class="btn btn-danger deleteEntry" style="display:none;"><i
                                            class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3 text-right">
                            <div class="col-md-12">
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

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <form id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_purchase_id">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title font-weight-bold">Edit Pembelian Barang</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Supplier</label>
                                <select name="supplier_id" id="edit_supplier_id" class="form-control select2-edit"
                                    required>
                                    @foreach ($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Tanggal Pembelian</label>
                                <input type="date" name="purchase_date" id="edit_purchase_date" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label>Catatan</label>
                                <input type="text" name="notes" id="edit_notes" class="form-control">
                            </div>
                        </div>
                        <hr>
                        <h6 class="font-weight-bold">Detail Barang <button type="button"
                                class="btn btn-sm btn-success float-right" id="addEditRow">+ Tambah Baris</button></h6>
                        <div id="editEntriesContainer">
                        </div>
                        <div class="row mt-3 text-right">
                            <div class="col-md-12">
                                <h4 class="font-weight-bold text-primary">Grand Total: <span id="editGrandTotalDisplay">Rp
                                        0</span></h4>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning font-weight-bold">Update Transaksi</button>
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
        // --- 1. FUNGSI HELPER GLOBAL ---
        function formatRupiah(angka) {
            if (!angka) return '0';
            let number_string = angka.toString().replace(/[^,\d]/g, ''),
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

        function calculateTotals(containerId, displayId) {
            let grandTotal = 0;
            $(`${containerId} .entry`).each(function() {
                let qty = parseFloat($(this).find('.qty-input').val()) || 0;
                let priceRaw = $(this).find('.price-input').val().replace(/\./g, '');
                let price = parseFloat(priceRaw) || 0;
                let subtotal = qty * price;
                $(this).find('.subtotal-input').val(formatRupiah(subtotal));
                grandTotal += subtotal;
            });
            $(displayId).text('Rp ' + formatRupiah(grandTotal));
        }

        // --- 2. LOGIKA JQUERY ---
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });

            // -- Event: Input Harga & Qty --
            $(document).on('keyup change', '.qty-input, .price-input', function() {
                if ($(this).hasClass('price-input')) {
                    $(this).val(formatRupiah($(this).val()));
                }
                calculateTotals('#newEntriesContainer', '#grandTotalDisplay');
                calculateTotals('#editEntriesContainer', '#editGrandTotalDisplay');
            });

            // -- Event: Tambah Baris (Create) --
            $('#addButton').on('click', function() {
                let row = $('#newEntriesContainer .entry:first').clone();
                row.find('input').val('');
                row.find('.deleteEntry').show();
                row.find('.select2-container').remove();
                $('#newEntriesContainer').append(row);
                row.find('select').select2({
                    width: '100%'
                });
            });

            // -- Event: Tambah Baris (Edit) --
            $('#addEditRow').on('click', function() {
                let row = `
                <div class="row entry mb-2 align-items-end">
                    <div class="col-md-4">
                        <select name="product_id[]" class="form-control select2-new-edit" required>
                            <option value="" disabled selected>Pilih Produk</option>
                            @foreach ($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><input type="number" name="quantity[]" class="form-control qty-input" min="1" required></div>
                    <div class="col-md-3"><input type="text" name="price[]" class="form-control price-input" required></div>
                    <div class="col-md-2"><input type="text" class="form-control subtotal-input bg-light" readonly></div>
                    <div class="col-md-1 text-right"><button type="button" class="btn btn-danger deleteEntry"><i class="fas fa-trash"></i></button></div>
                </div>`;
                $('#editEntriesContainer').append(row);
                $('.select2-new-edit').select2({
                    width: '100%'
                }).removeClass('select2-new-edit');
            });

            // -- Event: Hapus Baris --
            $(document).on('click', '.deleteEntry', function() {
                $(this).closest('.entry').remove();
                calculateTotals('#newEntriesContainer', '#grandTotalDisplay');
                calculateTotals('#editEntriesContainer', '#editGrandTotalDisplay');
            });

            // -- AJAX: Simpan (Store) --
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('dashboard.purchases.store') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        alert(res.message);
                        location.reload();
                    },
                    error: function() {
                        alert("Gagal menyimpan!");
                    }
                });
            });

            // -- AJAX: Tampilkan Detail (Show) --
            $(document).on('click', '.show-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('#det_po').text(data.purchase_number);
                    $('#det_date').text(data.purchase_date);
                    $('#det_sup').text(data.supplier.name);
                    $('#det_notes').text(data.notes || '-');
                    let itemsHtml = '';
                    data.items.forEach(function(item) {
                        itemsHtml +=
                            `<tr><td>${item.product.name}</td><td>Rp ${formatRupiah(item.price)}</td><td>${item.quantity}</td><td>Rp ${formatRupiah(item.subtotal)}</td></tr>`;
                    });
                    $('#detailItemsTable').html(itemsHtml);
                    $('#showModal').modal('show');
                });
            });

            // -- AJAX: Tombol Edit (Muat Data) --
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('#edit_purchase_id').val(data.id);
                    $('#edit_supplier_id').val(data.supplier_id).trigger('change');
                    $('#edit_purchase_date').val(data.purchase_date);
                    $('#edit_notes').val(data.notes);
                    let itemsHtml = '';
                    data.items.forEach(function(item) {
                        itemsHtml += `
                        <div class="row entry mb-2 align-items-end">
                            <div class="col-md-4">
                                <select name="product_id[]" class="form-control select2-edit" required>
                                    @foreach ($products as $prod)
                                        <option value="{{ $prod->id }}" ${item.product_id == {{ $prod->id }} ? 'selected' : ''}>{{ $prod->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><input type="number" name="quantity[]" class="form-control qty-input" value="${item.quantity}" min="1" required></div>
                            <div class="col-md-3"><input type="text" name="price[]" class="form-control price-input" value="${formatRupiah(item.price)}" required></div>
                            <div class="col-md-2"><input type="text" class="form-control subtotal-input bg-light" value="${formatRupiah(item.subtotal)}" readonly></div>
                            <div class="col-md-1 text-right"><button type="button" class="btn btn-danger deleteEntry"><i class="fas fa-trash"></i></button></div>
                        </div>`;
                    });
                    $('#editEntriesContainer').html(itemsHtml);
                    $('.select2-edit').select2({
                        width: '100%'
                    });
                    calculateTotals('#editEntriesContainer', '#editGrandTotalDisplay');
                    $('#editModal').modal('show');
                });
            });

            // -- AJAX: Update --
            // GANTI BAGIAN INI
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#edit_purchase_id').val();
                $.ajax({
                    url: "{{ url('dashboard/purchases') }}/" + id,
                    type: "POST", // Tetap POST karena kita pakai spoofing _method di bawah
                    data: $(this)
                .serialize(), // Ini akan otomatis mengirimkan token CSRF dan _method PUT
                    success: function(res) {
                        alert(res.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        alert("Gagal update data! Cek console untuk detail.");
                        console.log(xhr.responseText);
                    }
                });
            });
            // -- AJAX: Hapus --
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
