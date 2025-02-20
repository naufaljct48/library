<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();
        return view('admin.books.index', compact('books'));
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

        return redirect()->route('admin.books.index')->with('swal', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'Buku berhasil ditambahkan'
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

        return redirect()->route('admin.books.index')->with('swal', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'Buku berhasil diupdate'
        ]);
    }

    public function destroy(Book $book)
    {
        // Cek apakah buku sedang dipinjam
        if (!$book->is_available) {
            return response()->json([
                'message' => 'Buku sedang dipinjam'
            ], 400);
        }

        $book->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus'
        ]);
    }
}
