@extends('dashboard.layouts.admin')

@section('title', 'Konfirmasi Pembelian')

@push('styles')
    <style>
        :root {
            --c-radius: 12px;
        }

        /* ── HEADER KUNING ──────────────────────────────────────────── */
        .conf-header-card {
            background: linear-gradient(135deg, #E65100 0%, #F57F17 50%, #F9A825 100%);
            border-radius: var(--c-radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(245, 127, 23, .30);
        }

        .conf-header-card h4 {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0;
        }

        .conf-header-card p {
            color: rgba(255, 255, 255, .8);
            font-size: .82rem;
            margin: 2px 0 0;
        }

        .conf-pending-pill {
            background: rgba(255, 255, 255, .22);
            border: 1.5px solid rgba(255, 255, 255, .35);
            color: #fff;
            font-size: .82rem;
            font-weight: 700;
            padding: 7px 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-back-hdr {
            background: rgba(255, 255, 255, .18);
            border: 1.5px solid rgba(255, 255, 255, .35);
            color: #fff;
            font-size: .82rem;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all .2s;
        }

        .btn-back-hdr:hover {
            background: rgba(255, 255, 255, .3);
            color: #fff;
            text-decoration: none;
        }

        /* ── TABLE CARD ─────────────────────────────────────────────── */
        .conf-table-card {
            background: #fff;
            border-radius: var(--c-radius);
            box-shadow: 0 2px 16px rgba(245, 127, 23, .08);
            overflow: hidden;
        }

        .conf-table-card .p-3 {
            padding: 20px !important;
        }

        #confirmationTable thead th {
            background: #FFF8E1 !important;
            color: #795548;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            border: none !important;
            padding: 12px 14px !important;
            white-space: nowrap;
        }

        #confirmationTable tbody td {
            padding: 12px 14px !important;
            vertical-align: middle !important;
            border-top: 1px solid #FFF8E1 !important;
            font-size: .84rem;
            color: #2C3E50;
        }

        #confirmationTable tbody tr:hover {
            background: #FFFDE7;
        }

        #confirmationTable {
            border-collapse: collapse !important;
        }

        .po-code {
            font-family: monospace;
            font-size: .78rem;
            background: #FFF3E0;
            color: #E65100;
            padding: 3px 8px;
            border-radius: 5px;
            font-weight: 700;
        }

        /* Action group */
        .action-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: nowrap;
        }

        .btn-conf {
            padding: 6px 12px;
            border-radius: 7px;
            font-size: .76rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all .2s;
            white-space: nowrap;
        }

        .btn-conf-info {
            background: #E3F2FD;
            color: #1565C0;
        }

        .btn-conf-info:hover {
            background: #BBDEFB;
            transform: translateY(-1px);
        }

        .btn-conf-approve {
            background: linear-gradient(135deg, #2E7D32, #43A047);
            color: #fff;
            box-shadow: 0 2px 8px rgba(46, 125, 50, .2);
        }

        .btn-conf-approve:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, .3);
        }

        .btn-conf-cancel {
            background: linear-gradient(135deg, #C62828, #E53935);
            color: #fff;
            box-shadow: 0 2px 8px rgba(198, 40, 40, .2);
        }

        .btn-conf-cancel:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(198, 40, 40, .3);
        }

        /* Empty state */
        .empty-conf {
            text-align: center;
            padding: 50px 20px;
            color: #90A4AE;
        }

        .empty-conf i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 12px;
            color: #A5D6A7;
        }

        .empty-conf h6 {
            font-weight: 700;
            color: #546E7A;
            margin-bottom: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Header Kuning --}}
        <div class="conf-header-card">
            <div>
                <h4><i class="fas fa-hourglass-half mr-2"></i>Konfirmasi Pembelian Barang</h4>
                <p>Periksa dan setujui pesanan pembelian yang menunggu konfirmasi</p>
            </div>
            <div class="d-flex align-items-center gap-2" style="gap:10px; flex-wrap:wrap;">
                <div class="conf-pending-pill">
                    <i class="fas fa-clock"></i>
                    {{ $pendingPurchases->count() }} Menunggu Konfirmasi
                </div>
                <a href="{{ route('dashboard.purchases.index') }}" class="btn-back-hdr">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Alert info --}}
        <div class="alert d-flex align-items-start gap-2 mb-4"
            style="background:#E3F2FD; border:1.5px solid #90CAF9; border-radius:10px; color:#1565C0; padding:12px 16px; gap:10px;">
            <i class="fas fa-info-circle mt-1" style="flex-shrink:0;"></i>
            <span>
                <strong>Perhatian!</strong> Halaman ini menampilkan pesanan pembelian yang <strong>menunggu
                    konfirmasi</strong>.
                Periksa detail pesanan sebelum menyetujui atau membatalkannya.
                Stok produk akan bertambah otomatis setelah pesanan <strong>disetujui</strong>.
            </span>
        </div>

        {{-- Table --}}
        <div class="conf-table-card">
            <div class="p-3">
                @if ($pendingPurchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover w-100" id="confirmationTable">
                            <thead>
                                <tr>
                                    <th width="40px">No</th>
                                    <th>No PO</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Total</th>
                                    <th width="240px" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingPurchases as $i => $purchase)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td><span class="po-code">{{ $purchase->purchase_number }}</span></td>
                                        <td style="white-space:nowrap;">
                                            {{ \Carbon\Carbon::parse($purchase->purchase_date)->isoFormat('DD MMM YYYY') }}
                                        </td>
                                        <td>{{ $purchase->supplier->name }}</td>
                                        <td style="font-weight:700; color:#1565C0;">
                                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <div class="action-group">
                                                <button class="btn-conf btn-conf-info detail-btn"
                                                    data-id="{{ $purchase->id }}">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                                <button class="btn-conf btn-conf-approve approve-btn"
                                                    data-id="{{ $purchase->id }}">
                                                    <i class="fas fa-check-circle"></i> Setuju
                                                </button>
                                                <button class="btn-conf btn-conf-cancel cancel-btn"
                                                    data-id="{{ $purchase->id }}">
                                                    <i class="fas fa-times-circle"></i> Batalkan
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-conf">
                        <i class="fas fa-check-circle"></i>
                        <h6>Tidak Ada Pesanan Menunggu Konfirmasi</h6>
                        <p class="small mb-0">Semua pesanan pembelian sudah dikonfirmasi atau dibatalkan.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none;">
                <div class="modal-header text-white"
                    style="background:linear-gradient(135deg,#0288D1,#03A9F4); border:none;">
                    <h5 class="modal-title mb-0 font-weight-bold">
                        <i class="fas fa-file-invoice mr-2"></i>Detail Pesanan Pembelian
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1;">&times;</button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div style="background:#F8FAFD; border-radius:10px; padding:16px;">
                                <div class="d-flex justify-content-between py-2 border-bottom"><span
                                        class="text-muted small">No. PO</span><strong id="detail_po" class="small"
                                        style="font-family:monospace;"></strong></div>
                                <div class="d-flex justify-content-between py-2 border-bottom"><span
                                        class="text-muted small">Tanggal</span><span id="detail_date" class="small"></span>
                                </div>
                                <div class="d-flex justify-content-between py-2"><span
                                        class="text-muted small">Supplier</span><strong id="detail_supplier"
                                        class="small"></strong></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="background:#F8FAFD; border-radius:10px; padding:16px;">
                                <div class="d-flex justify-content-between py-2 border-bottom"><span
                                        class="text-muted small">Status</span><span id="detail_status"></span></div>
                                <div class="d-flex justify-content-between py-2 border-bottom"><span
                                        class="text-muted small">Email Supplier</span><span id="detail_email"
                                        class="small"></span></div>
                                <div class="d-flex justify-content-between py-2"><span
                                        class="text-muted small">Catatan</span><span id="detail_notes"
                                        class="small"></span></div>
                            </div>
                        </div>
                    </div>
                    <h6 class="font-weight-bold mb-3">Detail Produk</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead style="background:#FFF8E1;">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th width="18%">Harga Satuan</th>
                                    <th width="10%" class="text-center">Qty</th>
                                    <th width="18%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detail_items"></tbody>
                        </table>
                    </div>
                    <div class="text-right mt-3">
                        <span class="text-muted small">Total Pembayaran: </span>
                        <strong class="text-primary" id="detail_total" style="font-size:1.1rem;"></strong>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function formatRupiah(angka) {
            if (angka === undefined || angka === null) return '0';
            let number = Math.round(parseFloat(angka));
            if (isNaN(number)) return '0';
            let s = number.toString();
            let sisa = s.length % 3;
            let rupiah = s.substr(0, sisa);
            let ribuan = s.substr(sisa).match(/\d{3}/gi);
            if (ribuan) rupiah += (sisa ? '.' : '') + ribuan.join('.');
            return rupiah;
        }

        $(document).ready(function() {

            @if ($pendingPurchases->count() > 0)
                $('#confirmationTable').DataTable({
                    order: [
                        [2, 'desc']
                    ],
                    language: {
                        search: '',
                        searchPlaceholder: 'Cari PO, supplier...',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                        paginate: {
                            previous: '‹',
                            next: '›'
                        },
                    },
                    dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
                });
            @endif

            // Detail
            $(document).on('click', '.detail-btn', function() {
                let id = $(this).data('id');
                $.get("{{ url('dashboard/purchases') }}/" + id, function(data) {
                    $('#detail_po').text(data.purchase_number);
                    let d = new Date(data.purchase_date);
                    $('#detail_date').text(d.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }));
                    $('#detail_supplier').text(data.supplier.name);
                    $('#detail_email').text(data.supplier.email || '-');
                    $('#detail_notes').text(data.notes || '-');
                    $('#detail_total').text('Rp ' + formatRupiah(data.total_amount));

                    let badge = data.status === 'received' ?
                        '<span style="background:#E8F5E9;color:#2E7D32;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;"><i class="fas fa-check-circle"></i> Selesai</span>' :
                        data.status === 'cancelled' ?
                        '<span style="background:#FFEBEE;color:#C62828;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;"><i class="fas fa-times-circle"></i> Batal</span>' :
                        '<span style="background:#FFF8E1;color:#F57F17;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;"><i class="fas fa-hourglass-half"></i> Pending</span>';
                    $('#detail_status').html(badge);

                    let html = '';
                    data.items.forEach(item => {
                        html += `<tr>
                    <td>${item.product.name}</td>
                    <td class="text-right">Rp ${formatRupiah(item.price)}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td class="text-right font-weight-bold">Rp ${formatRupiah(item.subtotal)}</td>
                </tr>`;
                    });
                    $('#detail_items').html(html);
                    $('#detailModal').modal('show');
                });
            });

            // Approve
            $(document).on('click', '.approve-btn', function() {
                let id = $(this).data('id');
                let row = $(this).closest('tr');
                let po = row.find('td:nth-child(2)').text().trim();

                Swal.fire({
                    title: 'Setujui Pesanan?',
                    html: `Pesanan <strong>${po}</strong> akan disetujui.<br>
                   <small class="text-success"><i class="fas fa-info-circle"></i> Stok produk akan otomatis bertambah.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2E7D32',
                    cancelButtonColor: '#78909C',
                    confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Setujui!',
                    cancelButtonText: 'Batal',
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post("{{ url('dashboard/purchases') }}/" + id + "/approve", {
                                _token: "{{ csrf_token() }}"
                            },
                            function(res) {
                                Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: res.message,
                                        confirmButtonColor: '#1565C0',
                                        timer: 2000,
                                        showConfirmButton: false
                                    })
                                    .then(() => location.reload());
                            }
                        ).fail(xhr => Swal.fire('Error!', xhr.responseJSON?.message ||
                            'Gagal menyetujui!', 'error'));
                    }
                });
            });

            // Cancel
            $(document).on('click', '.cancel-btn', function() {
                let id = $(this).data('id');
                let row = $(this).closest('tr');
                let po = row.find('td:nth-child(2)').text().trim();

                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    html: `Pesanan <strong>${po}</strong> akan dibatalkan.<br>
                   <small class="text-muted">Pesanan tetap tersimpan dengan status Dibatalkan.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#C62828',
                    cancelButtonColor: '#78909C',
                    confirmButtonText: '<i class="fas fa-times mr-1"></i> Ya, Batalkan!',
                    cancelButtonText: 'Kembali',
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post("{{ url('dashboard/purchases') }}/" + id + "/cancel", {
                                _token: "{{ csrf_token() }}"
                            },
                            function(res) {
                                Swal.fire({
                                        icon: 'success',
                                        title: 'Dibatalkan!',
                                        text: res.message,
                                        confirmButtonColor: '#1565C0',
                                        timer: 2000,
                                        showConfirmButton: false
                                    })
                                    .then(() => location.reload());
                            }
                        ).fail(xhr => Swal.fire('Error!', xhr.responseJSON?.message ||
                            'Gagal membatalkan!', 'error'));
                    }
                });
            });
        });
    </script>
@endpush
