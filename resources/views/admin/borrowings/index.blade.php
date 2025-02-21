@extends('layouts.dashboard')

@section('title', 'Borrowing History')

@section('content')
<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Borrowings</h3>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="table-responsive">
                    <table id="borrowings-table" class="table card-table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Book</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($borrowings as $borrowing)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-xs me-2 rounded">
                                            {{ substr($borrowing->user->name, 0, 2) }}
                                        </span>
                                        {{ $borrowing->user->name }}
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $borrowing->book->title }}</div>
                                    <div class="text-muted">{{ $borrowing->book->author }}</div>
                                </td>
                                <td>{{ $borrowing->borrow_date->format('d M Y') }}</td>
                                <td>{{ $borrowing->due_date->format('d M Y') }}</td>
                                <td>
                                    @if($borrowing->returned_at)
                                        {{ $borrowing->returned_at->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if(!$borrowing->returned_at)
                                        @if($borrowing->due_date->isPast())
                                            <span class="badge bg-red text-red-fg">Overdue</span>
                                        @else
                                            <span class="badge bg-yellow text-yellow-fg">Borrowed</span>
                                        @endif
                                    @else
                                        @if($borrowing->returned_at->gt($borrowing->due_date))
                                            <span class="badge bg-orange text-orange-fg">Returned Late</span>
                                        @else
                                            <span class="badge bg-green text-green-fg">Returned</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(!$borrowing->returned_at)
                                        <button class="btn btn-sm btn-success return-book"
                                            data-id="{{ $borrowing->id }}"
                                            data-book="{{ $borrowing->book->title }}"
                                            data-user="{{ $borrowing->user->name }}">
                                            <i class="ti ti-arrow-back-up me-1"></i> Return
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
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // DataTable initialization
        $('#borrowings-table').DataTable({
            responsive: true,
            order: [[2, 'desc']], // Sort by borrow date by default
            language: {
                search: '',
                searchPlaceholder: 'Search borrowings...'
            }
        });

        // Return Book Button Click
        $('.return-book').click(function() {
            const id = $(this).data('id');
            const book = $(this).data('book');
            const user = $(this).data('user');

            Swal.fire({
                title: 'Return Book',
                html: `
                    <div class="text-start">
                        <p>Confirm return of:<br>
                        <strong>${book}</strong><br>
                        borrowed by <strong>${user}</strong>?</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, return it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`/admin/borrowings/${id}/return`)
                        .then(response => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Book has been returned successfully',
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
                                text: error.response.data.message || 'Failed to return book'
                            });
                        });
                }
            });
        });
    });
</script>
@endpush 