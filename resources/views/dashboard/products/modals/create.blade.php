{{-- ╔══════════════════════════════════════════════════════╗
     ║  MODAL — CREATE PRODUCT                              ║
     ╚══════════════════════════════════════════════════════╝ --}}

<style>
    /* ── Shared Modal Design Tokens ──────────────────────── */
    .modal-product .modal-content {
        border: none;
        border-radius: .85rem;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .18);
    }

    .modal-product .modal-header-brand {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        padding: 1.25rem 1.5rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .modal-product .modal-header-brand::after {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 130px;
        height: 130px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .07);
        pointer-events: none;
    }

    .modal-product .modal-icon {
        width: 38px;
        height: 38px;
        border-radius: .5rem;
        background: rgba(255, 255, 255, .18);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: .75rem;
    }

    .modal-product .modal-title-text {
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: .2px;
    }

    .modal-product .modal-subtitle {
        font-size: .73rem;
        color: rgba(255, 255, 255, .72);
        margin: .1rem 0 0;
    }

    /* Form Sections */
    .form-section-label {
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #858796;
        padding: .4rem 0;
        border-bottom: 1.5px solid #e3e6f0;
        margin-bottom: .9rem;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .form-section-label i {
        color: #4e73df;
    }

    /* Form Controls */
    .modal-product .form-label-custom {
        font-size: .78rem;
        font-weight: 700;
        color: #5a5c69;
        margin-bottom: .35rem;
        display: block;
    }

    .modal-product .form-control-custom {
        border: 1.5px solid #d1d3e2;
        border-radius: .5rem;
        padding: .5rem .85rem;
        font-size: .84rem;
        color: #5a5c69;
        transition: border-color .2s, box-shadow .2s;
        background: #fff;
        width: 100%;
    }

    .modal-product .form-control-custom:focus {
        outline: none;
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, .12);
    }

    .modal-product .form-control-custom::placeholder {
        color: #b7bac7;
    }

    /* Image Upload Zone */
    .img-upload-zone {
        border: 2px dashed #d1d3e2;
        border-radius: .65rem;
        padding: 1.2rem;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: #f8f9fc;
        position: relative;
        overflow: hidden;
    }

    .img-upload-zone:hover {
        border-color: #4e73df;
        background: #f0f4ff;
    }

    .img-upload-zone .upload-icon {
        font-size: 1.6rem;
        color: #b7bac7;
        transition: color .2s;
    }

    .img-upload-zone:hover .upload-icon {
        color: #4e73df;
    }

    .img-preview-wrap {
        margin-top: .75rem;
        position: relative;
        display: inline-block;
    }

    .img-preview-wrap img {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border-radius: .6rem;
        border: 2.5px solid #4e73df;
        box-shadow: 0 4px 12px rgba(78, 115, 223, .2);
    }

    .img-remove-btn {
        position: absolute;
        top: -6px;
        right: -6px;
        width: 22px;
        height: 22px;
        background: #e74a3b;
        border: none;
        border-radius: 50%;
        color: #fff;
        font-size: .6rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .2);
    }

    /* Stock / Price input group */
    .input-prefix {
        background: #eaecf4;
        border: 1.5px solid #d1d3e2;
        border-right: none;
        border-radius: .5rem 0 0 .5rem;
        padding: .5rem .85rem;
        font-size: .8rem;
        color: #858796;
        font-weight: 700;
    }

    .input-prefix+.form-control-custom {
        border-radius: 0 .5rem .5rem 0;
    }

    /* Select styling */
    .select-custom {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23858796'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        padding-right: 2rem !important;
    }

    .select-custom:disabled {
        background-color: #f8f9fc !important;
        cursor: not-allowed;
    }

    /* Modal footer */
    .modal-product .modal-footer-custom {
        background: #f8f9fc;
        border-top: 1px solid #e3e6f0;
        padding: .9rem 1.25rem;
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
    }

    .btn-modal-cancel {
        font-size: .8rem;
        font-weight: 600;
        padding: .45rem 1rem;
        border-radius: .45rem;
        border: 1.5px solid #d1d3e2;
        background: #fff;
        color: #858796;
        transition: all .2s;
    }

    .btn-modal-cancel:hover {
        background: #f0f0f5;
    }

    .btn-modal-submit {
        font-size: .8rem;
        font-weight: 700;
        padding: .45rem 1.25rem;
        border-radius: .45rem;
        border: none;
        background: linear-gradient(135deg, #4e73df, #224abe);
        color: #fff;
        box-shadow: 0 4px 12px rgba(78, 115, 223, .35);
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }

    .btn-modal-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(78, 115, 223, .45);
        color: #fff;
    }
</style>

<div class="modal fade modal-product" id="modalCreateProduct" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header-brand">
                <div class="d-flex align-items-center">
                    <div class="modal-icon">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <div>
                        <div class="modal-title-text">Tambah Produk Baru</div>
                        <div class="modal-subtitle">Lengkapi informasi produk di bawah ini</div>
                    </div>
                </div>
                <button type="button" class="close text-white ml-auto" data-dismiss="modal" style="opacity:.8">
                    <span>&times;</span>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('dashboard.products.store') }}" method="POST" enctype="multipart/form-data"
                id="formCreateProduct">
                @csrf
                <div class="modal-body p-4">

                    {{-- Section: Informasi Dasar --}}
                    <div class="form-section-label">
                        <i class="fas fa-info-circle"></i> Informasi Dasar
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label-custom">
                                Nama Produk <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control-custom"
                                placeholder="Contoh: Whiskas Tuna 1kg" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label-custom">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-control-custom select-custom" required>
                                <option value="active">✅ Active</option>
                                <option value="inactive">❌ Inactive</option>
                            </select>
                        </div>
                    </div>

                    {{-- Section: Foto Produk --}}
                    <div class="form-section-label">
                        <i class="fas fa-image"></i> Foto Produk
                    </div>

                    <div class="img-upload-zone" id="uploadZoneCreate"
                        onclick="document.getElementById('imageCreate').click()">
                        <i class="fas fa-cloud-upload-alt upload-icon"></i>
                        <p class="mb-1 mt-2 font-weight-600" style="font-size:.84rem;color:#5a5c69">
                            Klik untuk pilih foto produk
                        </p>
                        <p class="mb-0 small text-muted">JPG, PNG, WEBP — maks. 2MB</p>
                        <input type="file" id="imageCreate" name="image" class="d-none" accept="image/*"
                            onchange="previewImageAdvanced('imageCreate', 'previewCreate', 'previewWrapCreate', 'uploadZoneCreate')">
                    </div>

                    <div class="img-preview-wrap d-none mt-2" id="previewWrapCreate" style="display:block!important">
                        <img id="previewCreate" src="" alt="Preview">
                        <button type="button" class="img-remove-btn"
                            onclick="removeImage('imageCreate','previewCreate','previewWrapCreate','uploadZoneCreate')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Section: Harga & Stok --}}
                    <div class="form-section-label mt-3">
                        <i class="fas fa-tags"></i> Harga & Stok
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
                                <input type="text" name="price" class="form-control-custom input-rupiah"
                                    placeholder="50.000" required style="border-radius:0 .5rem .5rem 0">
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
                                <input type="number" name="stock" class="form-control-custom" placeholder="10"
                                    min="0" required style="border-radius:0 .5rem .5rem 0">
                            </div>
                        </div>
                    </div>

                    {{-- Section: Kategori --}}
                    <div class="form-section-label">
                        <i class="fas fa-sitemap"></i> Kategori Produk
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">
                                Species (Induk) <span class="text-danger">*</span>
                            </label>
                            <select id="species_select" class="form-control-custom select-custom" required>
                                <option value="">-- Pilih Species --</option>
                                @foreach ($categories as $cat)
                                    @if (empty($cat->parent_id))
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">
                                Kategori Produk <span class="text-danger">*</span>
                            </label>
                            <select name="category_id" id="category_select" class="form-control-custom select-custom"
                                required disabled>
                                <option value="">-- Pilih Species Dulu --</option>
                            </select>
                            <span class="small text-muted mt-1 d-block">
                                <i class="fas fa-info-circle mr-1"></i>Pilih species untuk memuat kategori
                            </span>
                        </div>
                    </div>

                    {{-- Section: Detail --}}
                    <div class="form-section-label">
                        <i class="fas fa-align-left"></i> Deskripsi Produk
                    </div>

                    <div class="mb-1">
                        <label class="form-label-custom">
                            Detail Produk <span class="text-danger">*</span>
                        </label>
                        <textarea name="detail" class="form-control-custom" rows="3" placeholder="Tulis deskripsi lengkap produk..."
                            required></textarea>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer-custom">
                    <button type="button" class="btn-modal-cancel" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="fas fa-save"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
