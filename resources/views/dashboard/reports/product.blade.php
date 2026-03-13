@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary mb-3">Laporan Penjualan Per Produk</h6>
            <div class="row">
                <div class="col-md-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" id="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Tanggal Selesai</label>
                    <input type="date" id="end_date" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="filter" class="btn btn-primary btn-block">Filter</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped" id="table-product-report" width="100%">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga Saat Ini</th>
                            <th>Jumlah Terjual</th>
                            <th>Total Omzet</th>
                            <th>Rata-rata/Item</th>
                        </tr>
                    </thead>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <th colspan="3" class="text-center">Total Seluruhnya</th>
                            <th class="text-center"></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Cek apakah fungsi DataTable sudah ada
            if ($.isFunction($.fn.DataTable)) {
                let table = $('#table-product-report').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dashboard.reports.product') }}",
                        data: function(d) {
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'terjual',
                            name: 'terjual',
                            className: 'text-center'
                        },
                        {
                            data: 'omzet',
                            name: 'omzet'
                        },
                        {
                            data: 'rata_rata',
                            name: 'rata_rata',
                            searchable: false
                        }
                    ],
                    footerCallback: function(row, data, start, end, display) {
                        var api = this.api();

                        // Fungsi pembantu yang diperbarui untuk menghapus Rp, titik, dan teks "Pcs"
                        var intVal = function(i) {
                            if (typeof i === 'string') {
                                // Menghapus Rp, titik, koma, dan kata "Pcs" agar tersisa angkanya saja
                                return i.replace(/[\Rp\.]/g, '').replace(/,/g, '.').replace(
                                    /[^0-9.-]+/g, "") * 1;
                            }
                            return typeof i === 'number' ? i : 0;
                        };

                        // 1. Hitung Total Terjual (Sekarang aman dari teks "Pcs")
                        let totalTerjual = api.column(3).data().reduce((a, b) => intVal(a) + intVal(b),
                            0);

                        // 2. Hitung Total Omzet
                        let totalOmzet = api.column(4).data().reduce((a, b) => intVal(a) + intVal(b),
                            0);

                        // 3. Hitung Rata-rata Keseluruhan
                        let rataRata = totalTerjual > 0 ? totalOmzet / totalTerjual : 0;

                        // Update isi footer HTML
                        $(api.column(3).footer()).html(totalTerjual.toLocaleString('id-ID') + ' Pcs');
                        $(api.column(4).footer()).html('Rp ' + totalOmzet.toLocaleString('id-ID'));
                        $(api.column(5).footer()).html('Rp ' + Math.round(rataRata).toLocaleString(
                            'id-ID'));
                    }
                });

                // Aksi tombol filter
                $('#filter').click(function() {
                    table.draw();
                });

            } else {
                console.error("DataTable library tidak terdeteksi!");
            }
        });
    </script>
@endpush
