<div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User: {{ $user->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST" action="{{ route('dashboard.users.update', $user->id) }}">
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
                        <strong>Role:</strong>
                        <div class="border rounded p-3 mt-2" style="max-height: 180px; overflow-y: auto;">
                            @foreach ($roles as $value => $label)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        id="editUser{{ $user->id }}Role{{ \Illuminate\Support\Str::slug($value, '-') }}" value="{{ $value }}"
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
