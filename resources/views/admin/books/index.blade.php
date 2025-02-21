@extends('layouts.dashboard')

@section('title', 'Manage Books')

@section('content')
<div class="row row-deck row-cards">
<div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Books</div>
                </div>
                <div class="h1 mb-3">{{ $totalBooks }}</div>
                <div class="d-flex mb-2">
                    <div>Available for borrowing</div>
                    <div class="ms-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                            {{ $availableBooks }}
                            <i class="ti ti-book ms-1"></i>
                        </span>
                    </div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" style="width: {{ ($availableBooks/$totalBooks)*100 }}%" role="progressbar">
                        <span class="visually-hidden">{{ ($availableBooks/$totalBooks)*100 }}% Available</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Book List</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                    <i class="ti ti-plus"></i> Add Book
                </button>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="table-responsive">
                    <table id="books-table" class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                            <tr>
                                <td>{{ $book->title }}</td>
                                <td>{{ $book->author }}</td>
                                <td>{{ Str::limit($book->description, 50) }}</td>
                                <td>
                                    @if($book->is_available)
                                    <span class="badge bg-success text-success-fg">Available</span>
                                    @else
                                    <span class="badge bg-warning text-warning-fg">Borrowed</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-book" 
                                        data-id="{{ $book->id }}"
                                        data-title="{{ $book->title }}"
                                        data-author="{{ $book->author }}"
                                        data-description="{{ $book->description }}">
                                        <i class="ti ti-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-book"
                                        data-id="{{ $book->id }}"
                                        data-title="{{ $book->title }}">
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

<!-- Modal Add Book -->
<div class="modal modal-blur fade" id="addBookModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBookForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" class="form-control" name="author" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary ms-auto">
                        <i class="ti ti-plus"></i> Add Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Book -->
<div class="modal modal-blur fade" id="editBookModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBookForm">
                <input type="hidden" name="book_id" id="edit_book_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="edit_title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" class="form-control" name="author" id="edit_author" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="4" required></textarea>
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
        $('#books-table').DataTable({
            responsive: true,
            order: [[0, 'asc']]
        });

        // Add Book Form Submit
        $('#addBookForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            axios.post('{{ route("admin.books.store") }}', formData)
                .then(response => {
                    $('#addBookModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Book has been added successfully',
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
                        text: error.response.data.message || 'Failed to add book'
                    });
                });
        });

        // Edit Book Button Click
        $('.edit-book').click(function() {
            const id = $(this).data('id');
            const title = $(this).data('title');
            const author = $(this).data('author');
            const description = $(this).data('description');

            $('#edit_book_id').val(id);
            $('#edit_title').val(title);
            $('#edit_author').val(author);
            $('#edit_description').val(description);

            $('#editBookModal').modal('show');
        });

        // Edit Book Form Submit
        $('#editBookForm').submit(function(e) {
            e.preventDefault();
            const id = $('#edit_book_id').val();
            const formData = {
                _method: 'PUT',
                title: $('#edit_title').val(),
                author: $('#edit_author').val(),
                description: $('#edit_description').val()
            };

            axios.post(`/admin/books/${id}`, formData)
                .then(response => {
                    $('#editBookModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Book has been updated successfully',
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
                        text: error.response.data.message || 'Failed to update book'
                    });
                });
        });

        // Delete Book Button Click
        $('.delete-book').click(function() {
            const id = $(this).data('id');
            const title = $(this).data('title');

            Swal.fire({
                title: 'Are you sure?',
                text: `Delete book "${title}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/admin/books/${id}`)
                        .then(response => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Book has been deleted.',
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
                                text: error.response.data.message || 'Failed to delete book'
                            });
                        });
                }
            });
        });
    });
</script>
@endpush