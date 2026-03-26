<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form id="editSupplierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Nama Supplier</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Kota</label>
                            <input type="text" name="city" id="edit_city" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" id="edit_status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Telepon</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold">Alamat</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning btn-update-confirm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
