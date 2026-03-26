<div class="modal fade" id="modalCreateProduct" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('dashboard.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" class="form-control" placeholder="Whiskas 1kg"
                                    required>
                            </div>
                        </div>
                        <div class="form-group">
                            <strong>Supplier:</strong>
                            <select name="supplier_id" class="form-control">
                                <option value="">-- Pilih Supplier (Opsional) --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto Produk</label>
                        <input type="file" name="image" class="form-control" id="imageCreate"
                            onchange="previewImage('imageCreate', 'previewCreate')">

                        <div class="mt-2">
                            <img id="previewCreate" src="" width="150"
                                class="img-thumbnail shadow-sm d-none">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Harga:</strong>
                                <input type="text" name="price" class="form-control input-rupiah"
                                    placeholder="50.000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Stok:</strong>
                                <input type="number" name="stock" class="form-control" placeholder="10" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <strong>Kategori:</strong>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <optgroup label="{{ $cat->name }}">
                                    @foreach ($cat->children as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Cabang:</strong>
                        <select name="outlet_id" class="form-control" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach ($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <strong>Detail:</strong>
                        <textarea name="detail" class="form-control" style="height:100px" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
