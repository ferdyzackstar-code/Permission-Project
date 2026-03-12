@foreach ($outlets as $outlet)
    <div class="modal fade" id="showOutletModal{{ $outlet->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detail Outlet</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nama</th>
                            <td>: {{ $outlet->name }}</td>
                        </tr>
                        <tr>
                            <th>Telepon</th>
                            <td>: {{ $outlet->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>: {{ $outlet->address }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat pada</th>
                            <td>: {{ $outlet->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach
