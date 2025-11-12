<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = Auth::user()->rol->nombre;

        if (!in_array($userRole, $roles)) {
            abort(403); // Prohibido
        }

        return $next($request);
    }
}