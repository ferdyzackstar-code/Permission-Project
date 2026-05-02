{{-- ╔══════════════════════════════════════════════════════╗
     ║  MODAL — EDIT PRODUCT                                ║
     ╚══════════════════════════════════════════════════════╝ --}}

<div class="modal fade modal-product" id="modalEditProduct{{ $product->id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header-brand" style="background:linear-gradient(135deg,#f6c23e 0%,#d4a017 100%)">
                <div class="d-flex align-items-center">
                    <div class="modal-icon">
                        <i class="fas fa-edit text-white"></i>
                    </div>
                    <div>
                        <div class="modal-title-text">Edit Produk</div>
                        <div class="modal-subtitle">{{ $product->name }}</div>
                    </div>
                </div>
                <button type="button" class="close text-white ml-auto" data-dismiss="modal" style="opacity:.8">
                    <span>&times;</span>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('dashboard.products.update', $product->id) }}" method="POST"
                enctype="multipart/form-data" id="formEditProduct{{ $product->id }}">
                @csrf
                @method('PUT')

                <div class="modal-body p-4">

                    {{-- Section: Informasi Dasar --}}
                    <div class="form-section-label">
                        <i class="fas fa-info-circle" style="color:#f6c23e"></i> Informasi Dasar
                    </div>

                    <div class="row">
                        <div class="col-md-9 mb-3">
                            <label class="form-label-custom">
                                Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" value="{{ $product->name }}"
                                class="form-control-custom" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label-custom">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-control-custom select-custom" required>
                                <option value="active" {{ $product->status === 'active' ? 'selected' : '' }}>✅ Active
                                </option>
                                <option value="inactive" {{ $product->status === 'inactive' ? 'selected' : '' }}>❌
                                    Inactive</option>
                            </select>
                        </div>
                    </div>

                    {{-- Section: Foto Produk --}}
                    <div class="form-section-label">
                        <i class="fas fa-image" style="color:#f6c23e"></i> Foto Produk
                    </div>

                    @php
                        $editPath = 'storage/uploads/products/' . $product->image;
                        $previewUrl =
                            $product->image && file_exists(public_path($editPath))
                                ? asset($editPath)
                                : asset('storage/uploads/products/default-product.jpg');
                    @endphp

                    {{-- Current photo info strip --}}
                    <div class="d-flex align-items-center mb-3 p-2 rounded"
                        style="background:#fff9e6;border:1px solid #fce8a1;font-size:.78rem;color:#856404">
                        <i class="fas fa-image mr-2"></i>
                        <span>Foto saat ini tersimpan. Upload baru hanya jika ingin menggantinya.</span>
                    </div>

                    <div class="row align-items-start">
                        {{-- Current preview --}}
                        <div class="col-auto">
                            <div class="text-center">
                                <img id="previewEdit{{ $product->id }}" src="{{ $previewUrl }}" alt="Preview"
                                    style="width:100px;height:100px;object-fit:cover;border-radius:.6rem;border:2.5px solid #f6c23e;box-shadow:0 4px 12px rgba(246,194,62,.25)">
                                <div class="small text-muted mt-1">Pratinjau</div>
                            </div>
                        </div>
                        {{-- Upload zone --}}
                        <div class="col">
                            <div class="img-upload-zone" id="uploadZoneEdit{{ $product->id }}"
                                style="border-color:#fce8a1"
                                onclick="document.getElementById('imageEdit{{ $product->id }}').click()">
                                <i class="fas fa-exchange-alt upload-icon"></i>
                                <p class="mb-1 mt-2 font-weight-600" style="font-size:.82rem;color:#5a5c69">
                                    Klik untuk ganti foto
                                </p>
                                <p class="mb-0 small text-muted">JPG, PNG, WEBP — maks. 2MB</p>
                                <input type="file" id="imageEdit{{ $product->id }}" name="image" class="d-none"
                                    accept="image/*"
                                    onchange="previewImageAdvanced(
                                           'imageEdit{{ $product->id }}',
                                           'previewEdit{{ $product->id }}',
                                           null,
                                           'uploadZoneEdit{{ $product->id }}'
                                       )">
                            </div>
                        </div>
                    </div>

                    {{-- Section: Harga & Stok --}}
                    <div class="form-section-label mt-3">
                        <i class="fas fa-tags" style="color:#f6c23e"></i> Harga & Stok
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">
                                Harga (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-prefix">Rp</span>
                                </div>
                                <input type="text" name="price"
                                    value="{{ number_format($product->price, 0, ',', '.') }}"
                                    class="form-control-custom input-rupiah" required
                                    style="border-radius:0 .5rem .5rem 0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">
                                Stok (Pcs) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-prefix"><i class="fas fa-boxes"></i></span>
                                </div>
                                <input type="number" name="stock" value="{{ $product->stock }}"
                                    class="form-control-custom" min="0" required
                                    style="border-radius:0 .5rem .5rem 0">
                            </div>
                        </div>
                    </div>

                    {{-- Section: Kategori --}}
                    <div class="form-section-label">
                        <i class="fas fa-sitemap" style="color:#f6c23e"></i> Kategori Produk
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">
                                Species (Induk) <span class="text-danger">*</span>
                            </label>
                            <select class="form-control-custom select-custom species-edit"
                                data-product-id="{{ $product->id }}" required>
                                <option value="">-- Pilih Species --</option>
                                @foreach ($categories as $cat)
                                    @if (empty($cat->parent_id))
                                        <option value="{{ $cat->id }}"
                                            {{ (isset($product->category->parent_id) && $product->category->parent_id == $cat->id) ||
                                            $product->category_id == $cat->id
                                                ? 'selected'
                                                : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">
                                Kategori <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" id="category_edit{{ $product->id }}"
                                class="form-control-custom select-custom" required>
                                <option value="{{ $product->category_id }}">
                                    {{ $product->category->name ?? '-- Pilih Kategori --' }}
                                </option>
                            </select>
                            <span class="small text-muted mt-1 d-block">
                                <i class="fas fa-info-circle mr-1"></i>Ganti species untuk memuat ulang kategori
                            </span>
                        </div>
                    </div>

                    {{-- Section: Detail --}}
                    <div class="form-section-label">
                        <i class="fas fa-align-left" style="color:#f6c23e"></i> Deskripsi Produk
                    </div>

                    <div class="mb-1">
                        <label class="form-label-custom">
                            Detail Produk <span class="text-danger">*</span>
                        </label>
                        <textarea name="detail" class="form-control-custom" rows="3" required>{{ $product->detail }}</textarea>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer-custom">
                    <button type="button" class="btn-modal-cancel" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn-modal-submit"
                        style="background:linear-gradient(135deg,#f6c23e,#d4a017);box-shadow:0 4px 12px rgba(246,194,62,.4)">
                        <i class="fas fa-save"></i> Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
