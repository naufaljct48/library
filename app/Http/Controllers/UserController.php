<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
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
        $users = User::where('is_admin', false)->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users.index')->with('swal', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'User berhasil ditambahkan'
        ]);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed'
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('swal', [
            'type' => 'success',
            'title' => 'Berhasil',
            'text' => 'User berhasil diupdate'
        ]);
    }

    public function destroy(User $user)
    {
        // Cek apakah user memiliki peminjaman aktif
        if ($user->borrowings()->whereNull('returned_at')->exists()) {
            return response()->json([
                'message' => 'User masih memiliki peminjaman aktif'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}
