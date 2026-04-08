<div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST" action="{{ route('dashboard.users.update', $user->id) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <strong>Name:</strong>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Email:</strong>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <strong>Password:</strong>
                        <input type="password" name="password" class="form-control"
                            placeholder="Kosongkan jika tidak ganti">
                    </div>
                    <div class="form-group mb-2">
                        <strong>Confirm Password:</strong>
                        <input type="password" name="confirm-password" class="form-control"
                            placeholder="Kosongkan jika tidak ganti">
                    </div>
                    <div class="form-group">
                        <label>Foto Profil</label>
                        <div class="custom-file mb-3">
                            <input type="file" name="image" class="custom-file-input"
                                id="inputImage{{ $user->id }}" onchange="previewImage(this, '{{ $user->id }}')">
                            <label class="custom-file-label" for="inputImage{{ $user->id }}">Choose File</label>
                        </div>

                        <div
                            class="d-flex flex-column align-items-center justify-content-center border rounded p-3 bg-light">
                            @php
                                $path = 'storage/uploads/users/' . $user->image;
                                $url =
                                    $user->image && file_exists(public_path($path))
                                        ? asset($path)
                                        : asset('storage/uploads/users/default-user.jpg');
                            @endphp

                            <img id="previewEdit{{ $user->id }}" src="{{ $url }}" width="150"
                                height="150" class="img-thumbnail shadow-sm mb-2" style="object-fit: cover;">

                            <small class="text-muted italic">Pratinjau Foto</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <strong>Role:</strong>
                        <div class="border rounded p-3 mt-2" style="max-height: 180px; overflow-y: auto;">
                            @foreach ($roles as $value => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        id="editUser{{ $user->id }}Role{{ \Illuminate\Support\Str::slug($value, '-') }}"
                                        value="{{ $value }}"
                                        {{ in_array($value, $user->roles->pluck('name')->toArray()) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="editUser{{ $user->id }}Role{{ \Illuminate\Support\Str::slug($value, '-') }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted text-italic">*Pilih minimal satu role</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
