@extends('layouts.dashboard')

@section('title', 'Manage Users')

@section('content')
<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Users List</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="ti ti-plus"></i> Add User
                </button>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="table-responsive">
                    <table id="users-table" class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Active Borrowings</th>
                                <th>Total Borrowings</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->borrowings->whereNull('returned_at')->count() > 0 ? 'yellow' : 'green' }} text-{{ $user->borrowings->whereNull('returned_at')->count() > 0 ? 'yellow' : 'green' }}-fg">
                                        {{ $user->borrowings->whereNull('returned_at')->count() }}
                                    </span>
                                </td>
                                <td>{{ $user->borrowings->count() }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-user" 
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}">
                                        <i class="ti ti-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-user"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}">
                                        <i class="ti ti-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add User -->
<div class="modal modal-blur fade" id="addUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <i class="ti ti-plus"></i> Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal modal-blur fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="edit_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (optional)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <i class="ti ti-device-floppy me-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // DataTable initialization
        $('#users-table').DataTable({
            responsive: true,
            order: [[0, 'asc']]
        });

        // Add User Form Submit
        $('#addUserForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            axios.post('{{ route("admin.users.store") }}', formData)
                .then(response => {
                    $('#addUserModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'User has been added successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.response.data.message || 'Failed to add user'
                    });
                });
        });

        // Edit User Button Click
        $('.edit-user').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const email = $(this).data('email');

            $('#edit_user_id').val(id);
            $('#edit_name').val(name);
            $('#edit_email').val(email);

            $('#editUserModal').modal('show');
        });

        // Edit User Form Submit
        $('#editUserForm').submit(function(e) {
            e.preventDefault();
            const id = $('#edit_user_id').val();
            const formData = new FormData(this);
            formData.append('_method', 'PUT');

            axios.post(`/admin/users/${id}`, formData)
                .then(response => {
                    $('#editUserModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'User has been updated successfully',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.response.data.message || 'Failed to update user'
                    });
                });
        });

        // Delete User Button Click
        $('.delete-user').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Are you sure?',
                text: `Delete user "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/admin/users/${id}`)
                        .then(response => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'User has been deleted.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: error.response.data.message || 'Failed to delete user'
                            });
                        });
                }
            });
        });
    });
</script>
@endpush 