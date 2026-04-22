@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-neon">Transaction Purchases</h2>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="fas fa-plus"></i> Add New
            </button>
        </div>

        <div class="card glass-card shadow-sm border-0">
            <div class="card-body">
                <table class="table table-hover w-100" id="purchaseTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Include Modals --}}
    @include('dashboard.purchases.modals.create')
    @include('dashboard.purchases.modals.edit')
    @include('dashboard.purchases.modals.show')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            var table = $('#purchaseTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('dashboard.purchases.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date'
                    },
                    {
                        data: 'purchase_number',
                        name: 'purchase_number'
                    },
                    {
                        data: 'supplier.name',
                        name: 'supplier.name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Create
            $('#createForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('dashboard.purchases.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#createModal').modal('hide');
                        $('#createForm')[0].reset();
                        table.ajax.reload();
                    }
                });
            });

            // Show
            $(document).on('click', '.show-btn', function() {
                let id = $(this).data('id');
                $.get("purchases/" + id, function(data) {
                    $('#show_number').text(data.purchase_number);
                    $('#show_supplier').text(data.supplier.name);
                    $('#show_date').text(data.purchase_date);
                    $('#show_status').html(
                        `<span class="badge bg-${data.status == 'received' ? 'success' : 'warning'}">${data.status}</span>`
                        );
                    $('#show_notes').text(data.notes ?? '-');
                    $('#showModal').modal('show');
                });
            });

            // Edit
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');
                $.get("purchases/" + id + "/edit", function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_supplier_id').val(data.supplier_id);
                    $('#edit_status').val(data.status);
                    $('#editModal').modal('show');
                });
            });

            // Update
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                $.ajax({
                    url: "purchases/" + id,
                    method: "PUT",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#editModal').modal('hide');
                        table.ajax.reload();
                    }
                });
            });

            // Delete
            $(document).on('click', '.delete-btn', function() {
                if (confirm('Hapus data ini?')) {
                    let id = $(this).data('id');
                    $.ajax({
                        url: "purchases/" + id,
                        method: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function() {
                            table.ajax.reload();
                        }
                    });
                }
            });
        });
    </script>
@endpush
