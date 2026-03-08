<div class="modal fade" id="modalShowUser{{ $user->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Profile</h5>
                <button type="button" class="close" data-dismiss="modal"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="avatar bg-info text-white rounded-circle d-inline-block p-4 mb-2">
                        <i class="fa fa-user fa-3x"></i>
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <span class="text-muted">{{ $user->email }}</span>
                </div>
                <hr>
                <div class="form-group">
                    <strong>Roles assigned:</strong><br>
                    @foreach ($user->getRoleNames() as $v)
                        <span class="badge badge-success px-3">{{ $v }}</span>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
