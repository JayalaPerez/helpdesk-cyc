<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Redirigir al usuario al login si no está autenticado.
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // Mensaje visual para login
            session()->flash('status', 'Debes iniciar sesión para continuar.');
            return route('login');
        }
    }
}
