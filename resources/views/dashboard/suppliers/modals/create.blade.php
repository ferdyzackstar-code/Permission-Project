<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle mr-2"></i>Tambah Supplier & Produk</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('dashboard.suppliers.store') }}" method="POST" id="supplierForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Nama Supplier (Vendor)</label>
                            <input type="text" name="name" class="form-control" placeholder="PT. Jurnal Karya"
                                required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Nama Produk</label>
                            <select name="product_id" class="form-control border-primary-50">
                                <option value="">-- Select Option --</option>
                                @foreach ($product as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}({{ $parent->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Harga Beli</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" name="purchase_price" class="form-control" placeholder="0"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold">Telepon</label>
                            <input type="text" name="phone" class="form-control" placeholder="0812...">
                        </div>
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold">Alamat</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
