<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Middleware untuk mengecek role user yang mengakses
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // if (!$request->user() || !$this->hasAnyRole($request->user(), $roles)) {
        //     return redirect()
        //         ->route('home')
        //         ->with('message_error', 'Anda tidak diizinkan masuk pada halaman ini.');
        // }
        return $next($request);
    }

    /**
     * Cek apakah user memiliki salah satu dari role yang diizinkan
     */
    private function hasAnyRole($user, array $roles)
    {
        foreach ($roles as $role) {
            if ($user->role_id === $role) {
                return true;
            }
        }
        return false;
    }
}
