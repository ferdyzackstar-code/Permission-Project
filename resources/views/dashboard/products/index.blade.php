@extends('dashboard.layouts.admin')

@push('styles')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
            <div class="mb-2 mb-lg-0">
                <h4 class="text-dark">Data Products</h4>
            </div>
            <div class="text-right">
                @can('product-create')
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCreateProduct">
                        <i class="fa fa-plus"></i> Create New Product
                    </button>
                @endcan
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @include('dashboard.products.modals.create')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive mt-2">
                <table class="table table-hover table-bordered" id="data-products">
                    <thead>
                        <tr class="bg-primary">
                            <th width='1px' class="text-center text-white">No</th>
                            <th class="text-center text-white">Name</th>
                            <th class="text-center text-white">Category</th>
                            <th class="text-center text-white">Cabang</th>
                            <th class="text-center text-white">Harga</th>
                            <th class="text-center text-white">Stok</th>
                            <th width='250px' class="text-center text-white">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @canany(['product-show', 'product-edit', 'product-delete'])
        @foreach ($products as $product)
            @can('product-show')
                @include('dashboard.products.modals.show', ['product' => $product])
            @endcan
            @can('product-edit')
                @include('dashboard.products.modals.edit', ['product' => $product])
            @endcan
        @endforeach
    @endcanany

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#data-products').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('dashboard.products.index') }}",
                    data: function(d) {
                        d.outlet_id = new URLSearchParams(window.location.search).get('outlet_id');
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'outlet_name',
                        name: 'outlet_name'
                    }, 
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [[1, 'asc']]
            });
        });

        $(document).on('click', '.show_confirm', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Yakin hapus data?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    customClass: {
                        confirmButton: 'swal2-confirm btn btn-danger mr-2',
                        cancelButton: 'swal2-cancel btn btn-secondary'
                    },
                    buttonsStyling: false,
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        form[0].submit();
                    }
                });
            } else {
                form[0].submit();
            }
        });

        // Fungsi Format Rupiah saat Mengetik
        $(document).on('keyup', '.input-rupiah', function() {
            $(this).val(formatRupiah($(this).val()));
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }

        // Saat Form dikirim, hilangkan titik agar database menerima angka murni (integer)
        $(document).on('submit', 'form', function() {
            $('.input-rupiah').each(function() {
                var cleanValue = $(this).val().replace(/\./g, '');
                $(this).val(cleanValue);
            });
        });
    </script>
@endpush
