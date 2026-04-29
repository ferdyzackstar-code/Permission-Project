@extends('dashboard.layouts.admin')

@section('title', 'Pengaturan Aplikasi')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Aplikasi</h1>
    </div>

    <form action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ── IDENTITAS & FOTO UTAMA ─────────────────────────────── --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-image mr-2"></i> Identitas & Foto Aplikasi
                </h6>
            </div>
            <div class="card-body">
                <div class="row">

                    {{-- Nama Aplikasi (Kiri) --}}
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <label class="font-weight-bold mb-2 d-block">
                            Nama Aplikasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="app_name"
                            class="form-control form-control-lg @error('app_name') is-invalid @enderror"
                            value="{{ old('app_name', $settings['app_name'] ?? '') }}" placeholder="Anda Petshop" required>
                        <small class="d-block text-muted mt-2">Nama aplikasi yang ditampilkan di sidebar dan halaman
                            login.</small>
                        @error('app_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Foto (Kanan) --}}
                    <div class="col-lg-6">
                        <label class="font-weight-bold mb-2 d-block">Foto Aplikasi</label>
                        <small class="d-block text-muted mb-3">
                            Digunakan di: <strong>Sidebar</strong>, <strong>Login</strong>, <strong>Register</strong>,
                            <strong>Lupa Password</strong>
                        </small>

                        {{-- File Input --}}
                        <div class="custom-file mb-3">
                            <input type="file" class="custom-file-input" id="appImageInput" name="app_image"
                                accept="image/*" onchange="previewImage(this)">
                            <label class="custom-file-label text-truncate" for="appImageInput" data-browse="Pilih">
                                Pilih file...
                            </label>
                        </div>

                        {{-- Preview Foto --}}
                        <div class="mt-3">
                            @if (!empty($settings['app_image']))
                                <div class="position-relative d-inline-block">
                                    <img id="imgPreview" src="{{ Storage::url($settings['app_image']) }}" alt="App Logo"
                                        class="shadow-sm"
                                        style="height:140px; width:140px; border-radius:12px; object-fit:cover; border:3px solid #f0f0f0; display:block;">
                                    <span class="badge badge-success position-absolute"
                                        style="bottom:5px; right:5px; font-size:11px;">
                                        <i class="fas fa-check-circle"></i> Aktif
                                    </span>
                                </div>
                            @else
                                <div class="d-flex align-items-center justify-content-center"
                                    style="height:140px; width:140px; background:#f5f5f5; border-radius:12px; border:2px dashed #ddd;">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-image" style="font-size:32px; opacity:0.5;"></i>
                                        <p style="font-size:11px; margin-top:8px;">Belum ada foto</p>
                                    </div>
                                </div>
                                <img id="imgPreview" src="" alt="" style="display:none;">
                            @endif
                        </div>
                        <p id="noImage" class="text-muted small mt-2" style="display:none;">Preview foto baru</p>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── TEKS PANEL AUTH ────────────────────────────────────── --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-lock mr-2"></i> Teks Panel Login & Register
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Teks ini tampil di panel ungu sisi kanan pada halaman Login dan Register.
                </p>

                <div class="row">
                    {{-- Login Title --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-heading text-primary mr-1"></i> Judul Panel — Login
                        </label>
                        <input type="text" name="auth_title_login" class="form-control"
                            value="{{ old('auth_title_login', $settings['auth_title_login'] ?? '') }}"
                            placeholder="Selamat Datang Kembali!">
                    </div>

                    {{-- Register Title --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-heading text-primary mr-1"></i> Judul Panel — Register
                        </label>
                        <input type="text" name="auth_title_register" class="form-control"
                            value="{{ old('auth_title_register', $settings['auth_title_register'] ?? '') }}"
                            placeholder="Halo, Kawan!">
                    </div>

                    {{-- Login Subtitle --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-align-left text-secondary mr-1"></i> Subjudul Panel — Login
                        </label>
                        <textarea name="auth_subtitle_login" class="form-control" rows="2"
                            placeholder="Masukkan detail pribadi Anda untuk menggunakan semua fitur situs">{{ old('auth_subtitle_login', $settings['auth_subtitle_login'] ?? '') }}</textarea>
                    </div>

                    {{-- Register Subtitle --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-align-left text-secondary mr-1"></i> Subjudul Panel — Register
                        </label>
                        <textarea name="auth_subtitle_register" class="form-control" rows="2"
                            placeholder="Daftarkan detail pribadi Anda untuk menggunakan semua fitur situs">{{ old('auth_subtitle_register', $settings['auth_subtitle_register'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── INFORMASI TOKO ──────────────────────────────────────── --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-store mr-2"></i> Informasi Toko
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Telepon --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-phone text-success mr-1"></i> No. Telepon
                        </label>
                        <input type="tel" name="store_phone" class="form-control"
                            value="{{ old('store_phone', $settings['store_phone'] ?? '') }}"
                            placeholder="085 xxx xxx xxxx">
                        <small class="d-block text-muted mt-1">Nomor telepon yang dapat dihubungi pelanggan.</small>
                    </div>

                    {{-- Alamat --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold d-block mb-2">
                            <i class="fas fa-map-marker-alt text-success mr-1"></i> Alamat Toko
                        </label>
                        <textarea name="store_address" class="form-control" rows="3"
                            placeholder="Jl. Contoh No. 1, RT/RW, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos">{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
                        <small class="d-block text-muted mt-1">Alamat lengkap untuk ditampilkan kepada pelanggan.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="d-flex justify-content-end mb-5">
            <a href="{{ route('dashboard.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save mr-1"></i> Simpan Pengaturan
            </button>
        </div>

    </form>
@endsection

@push('scripts')
    <script>
        // Preview image saat file dipilih
        function previewImage(input) {
            const preview = document.getElementById('imgPreview');
            const noImage = document.getElementById('noImage');
            const fileLabel = document.querySelector('.custom-file-label');

            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;

                // Update label dengan nama file
                fileLabel.textContent = fileName;

                // Preview gambar
                const reader = new FileReader();
                reader.onload = (e) => {
                    // Hapus div placeholder jika ada
                    const placeholder = preview.previousElementSibling;
                    if (placeholder && placeholder.classList.contains('d-flex')) {
                        placeholder.remove();
                    }

                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    preview.className = 'shadow-sm';
                    preview.setAttribute('style',
                        'height:140px; width:140px; border-radius:12px; object-fit:cover; border:3px solid #f0f0f0; display:block;'
                        );

                    if (noImage) noImage.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Update label saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const fileInput = document.getElementById('appImageInput');
            const fileLabel = document.querySelector('.custom-file-label');
            if (fileInput && fileLabel && fileInput.files && fileInput.files[0]) {
                fileLabel.textContent = fileInput.files[0].name;
            }
        });
    </script>
@endpush
