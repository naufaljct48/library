<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
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
        $books = Book::all();
        return view('admin.books.index', compact(
            'books',
            'totalBooks',
            'availableBooks',
        ));
    }

    public function create()
    {
        return view('admin.books.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'description' => 'required'
        ]);

        $validated['is_available'] = true;
        Book::create($validated);

        return response()->json([
            'message' => 'Book created successfully'
        ]);
    }

    public function edit(Book $book)
    {
        return view('admin.books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'description' => 'required'
        ]);

        $book->update($validated);

        return response()->json([
            'message' => 'Book updated successfully'
        ]);
    }

    public function destroy(Book $book)
    {
        if (!$book->is_available) {
            return response()->json([
                'message' => 'Cannot delete book that is currently borrowed'
            ], 400);
        }

        $book->delete();

        return response()->json([
            'message' => 'Book deleted successfully'
        ]);
    }
}
