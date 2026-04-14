@extends('dashboard.layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm border-0 animated--fade-in" style="border-radius: 15px; overflow: hidden;">

                    <div class="card-header bg-primary py-5 text-center">
                        <div class="position-relative d-inline-block">
                            <img src="{{ $user->image && file_exists(public_path('storage/uploads/users/' . $user->image)) ? asset('storage/uploads/users/' . $user->image) : asset('storage/uploads/users/default-user.jpg') }}"
                                class="rounded-circle img-thumbnail shadow"
                                style="width: 120px; height: 120px; object-fit: cover; border: 4px solid rgba(255,255,255,0.3);">
                        </div>
                        <h4 class="text-white mt-3 mb-0 font-weight-bold">{{ $user->name }}</h4>
                        <p class="text-white-50 small">{{ $user->email }}</p>
                    </div>

                    <div class="card-body p-4 text-center"> 
                        <p class="text-dark mb-4">{{ $user->bio ?? 'Belum ada bio...' }}</p>

                        <button type="button" class="btn btn-primary px-5 shadow-sm" data-toggle="modal"
                            data-target="#editProfileModal" style="border-radius: 10px;">
                            <i class="fas fa-edit mr-1"></i> Edit Profil
                        </button>
                        <a href="{{ url()->previous() }}" type="button" class="btn btn-secondary px-5 shadow-sm">
                            <i class="fa-regular fa-circle-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title font-weight-bold text-primary" id="editProfileModalLabel">
                        <i class="fas fa-user-edit mr-2"></i> Perbarui Profil Anda
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body px-4">
                        <div class="row">
                            <div class="col-md-4 text-center border-right">
                                <label class="text-muted small font-weight-bold d-block mb-3">FOTO PROFIL</label>
                                <div class="position-relative d-inline-block mb-3">
                                    <img id="previewProfile"
                                        src="{{ $user->image && file_exists(public_path('storage/uploads/users/' . $user->image)) ? asset('storage/uploads/users/' . $user->image) : asset('storage/uploads/users/default-user.jpg') }}"
                                        class="rounded-circle shadow-sm border"
                                        style="width: 140px; height: 140px; object-fit: cover;">
                                </div>

                                <div class="custom-file mt-2">
                                    <input type="file" name="image" class="custom-file-input" id="imgInput"
                                        onchange="previewImage('imgInput', 'previewProfile')">
                                    <label class="custom-file-label text-left" for="imgInput">Pilih Foto...</label>
                                    <small class="text-muted mt-2 d-block">Format: JPG, PNG (Max 2MB)</small>
                                </div>
                            </div>

                            <div class="col-md-8 pl-md-4">
                                <div class="form-group mb-4">
                                    <label class="text-muted small font-weight-bold"><i class="fas fa-id-card mr-1"></i>
                                        NAMA LENGKAP</label>
                                    <input type="text" name="name"
                                        class="form-control bg-light border-0 py-4 shadow-none"
                                        value="{{ old('name', $user->name) }}" required style="border-radius: 10px;">
                                </div>

                                <div class="form-group">
                                    <label class="text-muted small font-weight-bold"><i class="fas fa-pen mr-1"></i> BIO /
                                        TENTANG SAYA</label>
                                    <textarea name="bio" class="form-control bg-light border-0 shadow-none" rows="5"
                                        placeholder="Ceritakan sedikit tentang dirimu..." style="border-radius: 10px; resize: none;">{{ old('bio', $user->bio) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light px-4 font-weight-bold" data-dismiss="modal"
                            style="border-radius: 10px;">Batal</button>
                        <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm"
                            style="border-radius: 10px;">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
@endpush
