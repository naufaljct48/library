<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'book_id', 'borrow_date', 'due_date', 'returned_at'];

    // Menentukan field yang akan diubah menjadi instance Carbon
    protected $dates = [
        'borrow_date',
        'due_date',
        'returned_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
