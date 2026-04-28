<div class="modal fade" id="modalEditProduct{{ $product->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product: {{ $product->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{ route('dashboard.products.update', $product->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $product->name }}" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto Produk</label>
                        <input type="file" name="image" class="form-control" id="imageEdit{{ $product->id }}"
                            onchange="previewImage('imageEdit{{ $product->id }}', 'previewEdit{{ $product->id }}')">

                        <div
                            class="mt-3 d-flex flex-column align-items-center justify-content-center border rounded p-3 bg-light">
                            @php
                                $editPath = 'storage/uploads/products/' . $product->image;
                                $previewUrl =
                                    $product->image && file_exists(public_path($editPath))
                                        ? asset($editPath)
                                        : asset('storage/uploads/products/default-product.jpg');
                            @endphp

                            <img id="previewEdit{{ $product->id }}" src="{{ $previewUrl }}" width="150"
                                height="150" class="img-thumbnail shadow-sm mb-2"
                                style="object-fit: cover; display: block;"> <small class="text-muted italic">Pratinjau
                                Foto</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Harga:</strong>
                                <input type="text" name="price"
                                    value="{{ number_format($product->price, 0, ',', '.') }}"
                                    class="form-control input-rupiah" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Stok:</strong>
                                <input type="number" name="stock" value="{{ $product->stock }}" class="form-control"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Species:</strong>
                                <select class="form-control species-edit" data-product-id="{{ $product->id }}"
                                    required>
                                    <option value="">-- Pilih Species --</option>
                                    @foreach ($categories as $cat)
                                        @if (empty($cat->parent_id))
                                            <option value="{{ $cat->id }}"
                                                {{ (isset($product->category->parent_id) && $product->category->parent_id == $cat->id) || $product->category_id == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <strong>Kategori:</strong>
                                <select name="category_id" id="category_edit{{ $product->id }}"
                                    class="form-control category-edit" required>
                                    <option value="{{ $product->category_id }}">
                                        {{ $product->category->name ?? '-- Pilih Kategori --' }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <strong>Detail:</strong>
                        <textarea name="detail" class="form-control" style="height:100px" required>{{ $product->detail }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-update-confirm">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
