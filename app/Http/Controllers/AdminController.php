<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

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
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.borrowings', compact('borrowings'));
    }
}
