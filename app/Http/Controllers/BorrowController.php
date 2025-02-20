<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller
{
    public function borrow(Book $book)
    {
        // Cek apakah buku tersedia
        if (!$book->is_available) {
            return back()->with('swal', [
                'type' => 'error',
                'title' => 'Gagal',
                'text' => 'Buku sedang dipinjam'
            ]);
        }

        // Cek apakah user masih memiliki peminjaman aktif
        $activeBorrowing = Borrowing::where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->first();

        if ($activeBorrowing) {
            return back()->with('swal', [
                'type' => 'error',
                'title' => 'Gagal',
                'text' => 'Kamu masih memiliki buku yang belum dikembalikan'
            ]);
        }

        // Buat peminjaman baru dengan Carbon instance
        $now = Carbon::now();
        Borrowing::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'borrow_date' => $now,
            'due_date' => $now->copy()->addDays(7)
        ]);

        // Update status buku
        $book->update(['is_available' => false]);

        return back()->with('swal', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'Buku berhasil dipinjam'
        ]);
    }

    public function returnBook(Borrowing $borrowing)
    {
        // Update status peminjaman dengan Carbon instance
        $borrowing->update([
            'returned_at' => Carbon::now()
        ]);

        // Update status buku
        $borrowing->book->update(['is_available' => true]);

        return back()->with('swal', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'Buku berhasil dikembalikan'
        ]);
    }

    public function myBorrowings()
    {
        $activeBorrowing = Borrowing::with('book')
            ->where('user_id', Auth::id())
            ->whereNull('returned_at')
            ->first();

        $borrowingHistory = Borrowing::with('book')
            ->where('user_id', Auth::id())
            ->whereNotNull('returned_at')
            ->orderBy('returned_at', 'desc')
            ->get();

        return view('borrowings.index', compact('activeBorrowing', 'borrowingHistory'));
    }
}
