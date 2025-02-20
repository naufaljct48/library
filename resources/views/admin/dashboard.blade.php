@extends('layouts.dashboard')

@section('title', 'Dashboard')

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
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Total Users</div>
                </div>
                <div class="d-flex align-items-baseline">
                    <div class="h1 mb-0 me-2">{{ $totalUsers }}</div>
                    <div class="me-auto">
                        <span class="text-green d-inline-flex align-items-center lh-1">
                            {{ $activeUsers }}
                            <i class="ti ti-users ms-1"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Book List</h3>
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
                                    <span class="badge bg-success">Available</span>
                                    @else
                                    <span class="badge bg-warning">Borrowed</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.books.edit', $book->id) }}"
                                        class="btn btn-sm btn-primary"
                                        onclick="return confirm('Edit this book?')">
                                        Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-book"
                                        data-id="{{ $book->id }}"
                                        data-title="{{ $book->title }}">
                                        Delete
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#books-table').DataTable({
            responsive: true,
            order: [
                [0, 'asc']
            ]
        });

        // Handle delete book
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
                    // Send delete request
                    axios.delete(`/admin/books/${id}`)
                        .then(response => {
                            Swal.fire(
                                'Deleted!',
                                'Book has been deleted.',
                                'success'
                            ).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire(
                                'Error!',
                                'Failed to delete book.',
                                'error'
                            );
                        });
                }
            });
        });
    });
</script>
@endpush