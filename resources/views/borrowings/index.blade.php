@extends('layouts.dashboard')

@section('title', 'My Borrowings')

@section('content')
<div class="row row-deck row-cards">
    @if($activeBorrowing)
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Currently Borrowed</h3>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h3 class="mb-0">{{ $activeBorrowing->book->title }}</h3>
                        <div class="text-muted">{{ $activeBorrowing->book->author }}</div>
                        <p class="text-muted mb-0">{{ $activeBorrowing->book->description }}</p>
                        <div class="mt-3">
                            <span class="badge bg-blue text-blue-fg">Borrowed on: {{ $activeBorrowing->borrow_date->format('d-M-Y') }}</span>
                            <span class="badge bg-yellow text-yellow-fg">Due date: {{ $activeBorrowing->due_date->format('d-M-Y') }}</span>
                        </div>
                    </div>

                    <div class="col-md-8 text-end mt-2">
                        <button class="btn btn-primary return-book"
                            data-id="{{ $activeBorrowing->id }}"
                            data-title="{{ $activeBorrowing->book->title }}">
                            Return Book
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Borrowing History</h3>
            </div>
            <div id="table-default" class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Borrowed Date</th>
                            <th>Returned Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowingHistory as $borrow)
                        <tr>
                            <td>{{ $borrow->book->title }}</td>
                            <td>{{ $borrow->book->author }}</td>
                            <td>{{ $borrow->borrow_date->format('d M Y') }}</td>
                            <td>{{ $borrow->returned_at->format('d M Y') }}</td>
                            <td>
                                @if($borrow->returned_at->lt($borrow->due_date))
                                <span class="badge bg-green text-green-fg">Returned on time</span>
                                @else
                                <span class="badge bg-red text-red-fg">Returned late</span>
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('.datatable').DataTable({
            responsive: true,
            order: [
                [2, 'desc']
            ],
            language: {
                search: '',
                searchPlaceholder: 'Search history...',
                lengthMenu: '_MENU_ records per page',
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });

        // Handle return book
        $('.return-book').click(function() {
            const id = $(this).data('id');
            const title = $(this).data('title');

            Swal.fire({
                title: 'Return Book',
                html: `
                <div class="text-start">
                    <p>Are you sure you want to return:<br><strong>${title}</strong>?</p>
                    <div class="alert alert-info" role="alert">
                        <i class="ti ti-info-circle me-2"></i>
                        Please ensure the book is in good condition before returning.
                    </div>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, return it!',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return axios.post(`/return/${id}`)
                        .then(response => {
                            return response.data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                error.response.data.message || 'Failed to return book'
                            );
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Book has been returned successfully.',
                        icon: 'success',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection