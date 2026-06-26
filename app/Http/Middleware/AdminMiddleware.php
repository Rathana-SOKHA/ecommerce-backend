<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
         // 1. Check login
        if (!Auth::check()) {
            return redirect('/admin/login');
        }

        // 2. Check role
        if (Auth::user()->role !== 'admin') {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}