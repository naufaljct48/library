<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __construct()
    {
        if (!Auth::check() || Auth::user()->is_admin != 1) {
            abort(redirect('/dashboard')->with('swal', [
                'type' => 'error',
                'title' => 'Akses Ditolak',
                'text' => 'Kamu bukan admin.'
            ]));
        }
    }

    public function index()
    {
        $totalBooks = Book::count();
        $availableBooks = Book::where('is_available', true)->count();
        $totalUsers = User::count();
        $activeUsers = User::whereHas('borrowings', function ($query) {
            $query->whereNull('returned_at');
        })->count();

        $books = Book::all();

        return view('admin.dashboard', compact(
            'totalBooks',
            'availableBooks',
            'totalUsers',
            'activeUsers',
            'books'
        ));
    }

    public function borrowings()
    {
        $borrowings = Borrowing::with(['user', 'book'])
            ->orderBy('borrow_date', 'desc')
            ->get();

        return view('admin.borrowings.index', compact('borrowings'));
    }

    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->returned_at) {
            return response()->json([
                'message' => 'This book has already been returned'
            ], 400);
        }

        $borrowing->update([
            'returned_at' => now()
        ]);

        // Update book availability
        $borrowing->book->update([
            'is_available' => true
        ]);

        return response()->json([
            'message' => 'Book has been returned successfully'
        ]);
    }
}
