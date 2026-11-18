<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Elige el idioma de cada página antes de dibujarla.
 *
 * 1. Si la persona eligió uno con el selector, está en la sesión y se usa ese.
 * 2. Si no eligió, se usa el idioma del navegador (si es es, en o pt). Ese no se guarda,
 *    así sigue al navegador (igual que la app).
 */
class ElegirIdioma
{
    public function handle(Request $request, Closure $next): Response
    {
        $disponibles = ['es', 'en', 'pt'];

        $idioma = $request->session()->get('idioma');

        if (! in_array($idioma, $disponibles, true)) {
            $idioma = $request->getPreferredLanguage($disponibles) ?? 'es';
        }

        app()->setLocale($idioma);

        return $next($request);
    }
}
