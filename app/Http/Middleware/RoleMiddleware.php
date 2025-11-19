<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Si no hay usuario autenticado, denegar
        if (!Auth::check()) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        $user = Auth::user();

        // Soportar múltiples roles separados por coma o pipe (ej: "1,2" o "Admin|Docente")
        $allowed = preg_split('/[,|]/', $role);
        $allowed = array_map('trim', array_filter($allowed, fn($r) => $r !== ''));

        // Si todos los elementos son numéricos, comparar por rol_id
        $allNumeric = count($allowed) > 0 && collect($allowed)->every(fn($r) => is_numeric($r));

        $hasRole = false;

        if ($allNumeric) {
            $allowedIds = array_map('intval', $allowed);
            $hasRole = in_array(intval($user->rol_id), $allowedIds, true);
        } else {
            // Comparar por nombre de rol (case-insensitive) si el usuario tiene la relación cargada
            $userRoleName = $user->rol->nombre ?? null;
            if ($userRoleName) {
                foreach ($allowed as $a) {
                    if (strcasecmp($a, $userRoleName) === 0) {
                        $hasRole = true;
                        break;
                    }
                }
            }
        }

        if (!$hasRole) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
