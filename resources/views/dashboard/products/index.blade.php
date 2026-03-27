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
                            <th class="text-center text-white">Image</th>
                            <th class="text-center text-white">Name</th>
                            <th class="text-center text-white">Supplier</th>
                            <th class="text-center text-white">Species</th>
                            <th class="text-center text-white">Category</th>
                            <th class="text-center text-white">Status</th>
                            <th class="text-center text-white">Harga</th>
                            <th class="text-center text-white">Stok</th>
                            <th width='150px' class="text-center text-white">Actions</th>
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
            // 1. INITIALIZE DATATABLE
            // Simpan ke variabel agar bisa dipanggil jika perlu reload
            var table = $('#data-products').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('dashboard.products.index') }}",
                    data: function(d) {
                        d.outlet_id = new URLSearchParams(window.location.search).get('outlet_id');
                    },
                    error: function(xhr, error, thrown) {
                        console.log("Error DataTable: ", xhr.responseText);
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'species',
                        name: 'species'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
                columnDefs: [{
                        targets: [0, 1, 6, 8, 9],
                        className: "text-center align-middle"
                    },
                    {
                        targets: 7,
                        className: "text-right align-middle"
                    }
                ],
                order: [
                    [2, 'asc']
                ]
            });

            // 2. REUSABLE AJAX FUNCTION FOR DROPDOWN
            function fetchSubCategories(parentId, targetSelect) {
                if (parentId) {
                    // Reset dropdown dan beri indikator loading
                    targetSelect.empty().append('<option value="">Loading...</option>').prop('disabled', true);

                    $.ajax({
                        url: "{{ url('dashboard/get-subcategories') }}/" + parentId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            targetSelect.empty().append(
                                '<option value="">-- Pilih Kategori --</option>');
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    targetSelect.append('<option value="' + value.id + '">' +
                                        value.name + '</option>');
                                });
                                targetSelect.prop('disabled', false); // Aktifkan jika ada data
                            } else {
                                targetSelect.append('<option value="">Tidak ada sub-kategori</option>');
                                targetSelect.prop('disabled', true);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert('Gagal mengambil data kategori.');
                            targetSelect.prop('disabled', false).empty().append(
                                '<option value="">-- Error --</option>');
                        }
                    });
                } else {
                    targetSelect.prop('disabled', true).empty().append(
                        '<option value="">-- Pilih Species Dulu --</option>');
                }
            }

            // 3. LOGIC FOR MODAL CREATE
            // Pastikan ID #species_select dan #category_select ada di modals/create.blade.php
            $(document).on('change', '#species_select', function() {
                let speciesId = $(this).val();
                let categorySelect = $('#category_select');
                fetchSubCategories(speciesId, categorySelect);
            });

            // 4. LOGIC FOR MODAL EDIT
            $(document).on('change', '.species-edit', function() {
                let speciesId = $(this).val();
                let productId = $(this).data('product-id');
                let categorySelect = $('#category_edit' + productId);
                fetchSubCategories(speciesId, categorySelect);
            });

            // 5. RUPIAH FORMATTER
            $(document).on('keyup', '.input-rupiah', function() {
                $(this).val(formatRupiah($(this).val()));
            });

            function formatRupiah(angka) {
                let number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            }

            // Bersihkan format rupiah sebelum submit
            $(document).on('submit', 'form', function() {
                $(this).find('.input-rupiah').each(function() {
                    let val = $(this).val().replace(/\./g, '');
                    $(this).val(val);
                });
            });
        });

        // 6. IMAGE PREVIEW (Di luar document.ready agar terbaca onchange)
        function previewImage(inputId, previewId) {
            const image = document.getElementById(inputId);
            const imgPreview = document.getElementById(previewId);
            if (image.files && image.files[0]) {
                imgPreview.classList.remove('d-none');
                imgPreview.style.display = 'block';
                const oFReader = new FileReader();
                oFReader.readAsDataURL(image.files[0]);
                oFReader.onload = function(oFREvent) {
                    imgPreview.src = oFREvent.target.result;
                }
            }
        }
    </script>
@endpush
