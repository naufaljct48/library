<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->is_admin != 1) {
            return redirect('/dashboard')->with('swal', [
                'type' => 'error',
                'title' => 'Akses Ditolak',
                'text' => 'Kamu bukan admin.'
            ]);
        }

        return $next($request);
    }
}
