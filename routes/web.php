<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect('/login');
});

// Route untuk auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Route yang perlu auth
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/borrow/{book}', [BorrowController::class, 'borrow'])->name('borrow');
    Route::post('/return/{borrowing}', [BorrowController::class, 'returnBook'])->name('return');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/borrowings', [BorrowController::class, 'myBorrowings'])->name('borrowings.index');

    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::resource('books', BookController::class)->names('admin.books');
        Route::resource('users', UserController::class)->names('admin.users');
        Route::get('borrowings', [AdminController::class, 'borrowings'])->name('admin.borrowings.index');
        Route::post('borrowings/{borrowing}/return', [AdminController::class, 'returnBook']);
    });
});
