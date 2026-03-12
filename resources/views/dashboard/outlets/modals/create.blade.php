<div class="modal fade" id="addOutletModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.outlets.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Outlet</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Outlet</label>
                        <input type="text" name="name" class="form-control" placeholder="Nama Outlet" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="phone" class="form-control" placeholder="08xxx">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="address" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
