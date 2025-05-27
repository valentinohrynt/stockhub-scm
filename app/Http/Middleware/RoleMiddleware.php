<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        if ($user && $user->role && in_array(strtolower($user->role->name), array_map('strtolower', $roles))) {
            return $next($request);
        }

        return redirect()->route('restricted.page') 
                         ->with('error', 'You do not have permission to access this specific area.');
    }
}