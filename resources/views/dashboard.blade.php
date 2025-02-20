@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="row row-deck row-cards">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">My Active Borrowings</div>
                </div>
                <div class="h1 mb-3">{{ $activeBorrowings }}</div>
                <div class="d-flex mb-2">
                    <div>Books borrowed</div>
                    <div class="ms-auto">
                        <span class="text-yellow d-inline-flex align-items-center lh-1">
                            {{ $totalBorrowings }} Total
                            <i class="ti ti-books ms-1"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Available Books</h3>
            </div>
            <div id="table-default" class="table-responsive">
                <table id="available-books" class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availableBooks as $book)
                        <tr>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->author }}</td>
                            <td>{{ Str::limit($book->description, 50) }}</td>
                            <td>
                                @if($activeBorrowings > 0)
                                <button class="btn btn-secondary" disabled>
                                    Already have active borrowing
                                </button>
                                @else
                                <button class="btn btn-primary borrow-book"
                                    data-id="{{ $book->id }}"
                                    data-title="{{ $book->title }}">
                                    Borrow
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#available-books').DataTable({
            responsive: true,
            order: [
                [0, 'asc']
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search books...',
                lengthMenu: '_MENU_ books per page',
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });

        // Handle borrow book
        $('.borrow-book').click(function() {
            const id = $(this).data('id');
            const title = $(this).data('title');

            Swal.fire({
                title: 'Borrow Book',
                text: `Do you want to borrow "${title}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, borrow it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`/borrow/${id}`)
                        .then(response => {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Book has been borrowed.',
                                icon: 'success',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire(
                                'Error!',
                                error.response.data.message || 'Failed to borrow book.',
                                'error'
                            );
                        });
                }
            });
        });
    });
</script>
@endpush