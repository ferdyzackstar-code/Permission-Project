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
                @can('product.create')
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalCreateProduct">
                        <i class="fa fa-plus"></i> Create New Product
                    </button>
                @endcan
            </div>
        </div>
    </div>

    @if (session()->has('import_failures'))
        <div class="alert alert-danger" role="alert">
            <strong>Beberapa baris gagal diimport:</strong>
            <ul>
                @foreach (session()->get('import_failures') as $failure)
                    <li>
                        Baris ke-{{ $failure->row() }}:
                        @foreach ($failure->errors() as $error)
                            {{ $error }}
                        @endforeach
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('dashboard.products.modals.create')

    <div class="button-action" style="margin-bottom: 20px">
        <a href="{{ route('dashboard.products.downloadImportTemplate') }}" class="btn btn-warning btn-md">
            <i class="fas fa-file-download"></i> IMPORT TEMPLATE
        </a>

        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#import">
            <i class="fas fa-upload"></i> IMPORT
        </button>

        <a href="{{ route('dashboard.products.export') }}" class="btn btn-primary btn-md">
            <i class="fas fa-file-export"></i> EXPORT
        </a>
    </div>

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

    @canany(['product.show', 'product.edit', 'product.delete'])
        @foreach ($products as $product)
            @can('product.show')
                @include('dashboard.products.modals.show', ['product' => $product])
            @endcan
            @can('product.edit')
                @include('dashboard.products.modals.edit', ['product' => $product])
            @endcan
        @endforeach
    @endcanany

    <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">IMPORT DATA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('dashboard.products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>PILIH FILE EXCEL</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                            <small class="text-muted">Gunakan template yang sudah disediakan.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">TUTUP</button>
                        <button type="submit" class="btn btn-success">MULAI IMPORT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
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

            function fetchSubCategories(parentId, targetSelect) {
                if (parentId) {
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
                                targetSelect.prop('disabled', false);
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

            $(document).on('change', '#species_select', function() {
                let speciesId = $(this).val();
                let categorySelect = $('#category_select');
                fetchSubCategories(speciesId, categorySelect);
            });

            $(document).on('change', '.species-edit', function() {
                let speciesId = $(this).val();
                let productId = $(this).data('product-id');
                let categorySelect = $('#category_edit' + productId);
                fetchSubCategories(speciesId, categorySelect);
            });

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

            $(document).on('submit', 'form', function() {
                $(this).find('.input-rupiah').each(function() {
                    let val = $(this).val().replace(/\./g, '');
                    $(this).val(val);
                });
            });
        });

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
