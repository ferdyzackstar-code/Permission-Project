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
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-image mr-1"></i> Identitas & Foto Aplikasi
                </h6>
            </div>
            <div class="card-body">
                <div class="row align-items-start">

                    {{-- Nama Aplikasi --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Nama Aplikasi <span class="text-danger">*</span></label>
                        <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror"
                            value="{{ old('app_name', $settings['app_name'] ?? '') }}" required>
                        @error('app_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Foto --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Foto Aplikasi</label>
                        <small class="d-block text-muted mb-2">
                            Foto ini dipakai di: <strong>Sidebar</strong>, <strong>Login</strong>,
                            <strong>Register</strong>, dan <strong>Lupa Password</strong>.
                        </small>
                            onchange="previewImage(this)">
                        <div class="mt-3">
                            @if (!empty($settings['app_image']))
                                <p class="text-muted mb-1" style="font-size:12px;">Foto saat ini:</p>
                                <img id="imgPreview" src="{{ Storage::url($settings['app_image']) }}" alt="App Image"
                                    style="height:120px; border-radius:12px; object-fit:cover; border:2px solid #e0e0e0;">
                            @else
                                <img id="imgPreview" src="" alt=""
                                    style="display:none; height:120px; border-radius:12px; object-fit:cover; border:2px solid #e0e0e0;">
                                <p id="noImage" class="text-muted" style="font-size:12px;">Belum ada foto.</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ── TEKS PANEL AUTH ────────────────────────────────────── --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-lock mr-1"></i> Teks Panel Login & Register
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3" style="font-size:13px;">
                    Teks ini tampil di panel ungu sisi kanan pada halaman Login dan Register.
                </p>
                <div class="row">
                    {{-- Login --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Judul Panel — Login</label>
                        <input type="text" name="auth_title_login" class="form-control"
                            value="{{ old('auth_title_login', $settings['auth_title_login'] ?? '') }}"
                            placeholder="Selamat Datang Kembali!">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Subjudul Panel — Login</label>
                        <input type="text" name="auth_subtitle_login" class="form-control"
                            value="{{ old('auth_subtitle_login', $settings['auth_subtitle_login'] ?? '') }}"
                            placeholder="Masukkan detail pribadi Anda...">
                    </div>
                    {{-- Register --}}
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Judul Panel — Register</label>
                        <input type="text" name="auth_title_register" class="form-control"
                            value="{{ old('auth_title_register', $settings['auth_title_register'] ?? '') }}"
                            placeholder="Halo, Kawan!">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Subjudul Panel — Register</label>
                        <input type="text" name="auth_subtitle_register" class="form-control"
                            value="{{ old('auth_subtitle_register', $settings['auth_subtitle_register'] ?? '') }}"
                            placeholder="Daftarkan detail pribadi Anda...">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── INFORMASI TOKO ──────────────────────────────────────── --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-store mr-1"></i> Informasi Toko
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">No. Telepon</label>
                        <input type="text" name="store_phone" class="form-control"
                            value="{{ old('store_phone', $settings['store_phone'] ?? '') }}"
                            placeholder="+62 xxx xxxx xxxx">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Alamat Toko</label>
                        <textarea name="store_address" class="form-control" rows="2" placeholder="Jl. Contoh No. 1, Kota">{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Simpan --}}
        <div class="text-right mb-5">
            <button type="submit" class="btn btn-primary px-5">
                <i class="fas fa-save mr-1"></i> Simpan Pengaturan
            </button>
        </div>

    </form>
@endsection

@push('scripts')
    <script>
        function previewImage(input) {
            const preview = document.getElementById('imgPreview');
            const noImage = document.getElementById('noImage');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (noImage) noImage.style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
