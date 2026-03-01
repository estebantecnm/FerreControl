<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario no es rol 1 (Admin), lo rebotamos
        if ($request->user()->id_rol !== 1) {
            return response()->json(['msj' => 'No tienes permiso de administrador'], 403);
        }

        return $next($request);
    }
}
