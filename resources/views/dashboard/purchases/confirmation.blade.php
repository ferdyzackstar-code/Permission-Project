@extends('dashboard.layouts.admin')

@section('content')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('asset/css/purchases-style.css') }}">
    @endpush

    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Konfirmasi Pembelian Barang</h2>
            <a href="{{ route('dashboard.purchases.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Pembelian
            </a>
        </div>

        <!-- Info Alert -->
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i> <strong>Perhatian!</strong> Halaman ini menampilkan pesanan pembelian yang
            <strong>menunggu konfirmasi</strong>. Periksa detail pesanan sebelum menyetujui atau membatalkannya.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Table Pending Purchases -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-hourglass-half"></i> Pesanan Menunggu Konfirmasi
                    <span class="badge badge-warning ml-2">{{ $pendingPurchases->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                @if ($pendingPurchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="confirmationTable" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th width="3%" class="text-center">No</th>
                                    <th>No PO</th>
                                    <th>Tanggal Pembelian</th>
                                    <th>Supplier</th>
                                    <th>Total Pembayaran</th>
                                    <th width="22%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingPurchases as $index => $purchase)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge badge-dark">{{ $purchase->purchase_number }}</span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($purchase->purchase_date)->isoFormat('dddd, DD MMMM YYYY') }}
                                        </td>
                                        <td>{{ $purchase->supplier->name }}</td>
                                        <td class="font-weight-bold text-primary">
                                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center action-buttons">
                                            <button class="btn btn-sm btn-info detail-btn" data-id="{{ $purchase->id }}">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <button class="btn btn-sm btn-success approve-btn"
                                                data-id="{{ $purchase->id }}">
                                                <i class="fas fa-check"></i> Setuju
                                            </button>
                                            <button class="btn btn-sm btn-danger cancel-btn" data-id="{{ $purchase->id }}">
                                                <i class="fas fa-times"></i> Batalkan
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success text-center" role="alert">
                        <i class="fas fa-check-circle fa-2x mb-3"></i>
                        <h5>Tidak Ada Pesanan Menunggu Konfirmasi</h5>
                        <p class="mb-0">Semua pesanan pembelian sudah dikonfirmasi atau dibatalkan.</p>
                    </div>
                @endif
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
                                    <label class="detail-label">Tanggal Pembelian</label>
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
                                    <label class="detail-label">Email Supplier</label>
                                    <span class="detail-value" id="detail_email"></span>
                                </div>
                                <div class="detail-row">
                                    <label class="detail-label">Catatan</label>
                                    <span class="detail-value" id="detail_notes"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-weight-bold mb-3 border-bottom pb-2">Detail Produk</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th width="15%">Harga Satuan</th>
                                    <th width="10%">Jumlah</th>
                                    <th width="15%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detail_items"></tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-right">
                            <h5 class="font-weight-bold text-primary">
                                <i class="fas fa-calculator"></i> Total Pembayaran:
                                <span id="detail_total" class="text-danger"></span>
                            </h5>
                        </div>
                    </div>
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

    <script>
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

        $(document).ready(function() {
            // Initialize DataTable
            $('#confirmationTable').DataTable({
                "order": [
                    [2, "desc"]
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                }
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
                    $('#detail_email').text(data.supplier.email || '-');
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
                            '<span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> Pending</span>';
                    }
                    $('#detail_status').html(statusBadge);

                    let itemsHtml = '';
                    data.items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${item.product.name}</td>
                                <td class="text-right">Rp ${formatRupiah(item.price)}</td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-right font-weight-bold">Rp ${formatRupiah(item.subtotal)}</td>
                            </tr>
                        `;
                    });
                    $('#detail_items').html(itemsHtml);
                    $('#detailModal').modal('show');
                });
            });

            // Approve Button
            $(document).on('click', '.approve-btn', function() {
                let id = $(this).data('id');
                let poNumber = $(this).closest('tr').find('td:nth-child(2)').text();

                Swal.fire({
                    title: 'Setujui Pesanan?',
                    html: `Anda akan menyetujui pesanan <strong>${poNumber}</strong>.<br>Stok produk akan otomatis bertambah sesuai jumlah pesanan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check"></i> Ya, Setujui!',
                    cancelButtonText: '<i class="fas fa-times"></i> Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ url('dashboard/purchases') }}/" + id + "/approve", {
                            _token: "{{ csrf_token() }}"
                        }, function(res) {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                location.reload();
                            });
                        }).fail(function(xhr) {
                            let msg = xhr.responseJSON?.message ||
                                'Gagal menyetujui pesanan!';
                            Swal.fire('Error!', msg, 'error');
                        });
                    }
                });
            });

            // Cancel Button
            $(document).on('click', '.cancel-btn', function() {
                let id = $(this).data('id');
                let poNumber = $(this).closest('tr').find('td:nth-child(2)').text();

                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    html: `Anda akan membatalkan pesanan <strong>${poNumber}</strong>.<br>Pesanan akan tetap tersimpan dengan status Dibatalkan.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-times"></i> Ya, Batalkan!',
                    cancelButtonText: '<i class="fas fa-undo"></i> Jangan'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ url('dashboard/purchases') }}/" + id + "/cancel", {
                            _token: "{{ csrf_token() }}"
                        }, function(res) {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                location.reload();
                            });
                        }).fail(function(xhr) {
                            let msg = xhr.responseJSON?.message ||
                                'Gagal membatalkan pesanan!';
                            Swal.fire('Error!', msg, 'error');
                        });
                    }
                });
            });
        });
    </script>
@endpush
