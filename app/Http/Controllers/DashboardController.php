<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $activeBorrowings = Borrowing::where('user_id', $user->id)
            ->whereNull('returned_at')
            ->count();

        $totalBorrowings = Borrowing::where('user_id', $user->id)->count();

        $availableBooks = Book::where('is_available', true)->get();

        return view('dashboard', compact(
            'activeBorrowings',
            'totalBorrowings',
            'availableBooks'
        ));
    }
}
