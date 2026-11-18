{{--
    El molde de TODAS las páginas (home, Mis perfiles, login, registro…).
    Blade simple, sin estado y sin librerías de estilos.
    - {{ $slot }}: el contenido de cada página (como children en React).
    - {{ $head }}: algo extra para el <head>, por ejemplo el CSS de esa página.
    - $title: el título de la pestaña (opcional).
    Se usa con <x-layouts::app> en páginas Blade. Las páginas Livewire (Route::livewire)
    lo usan solas, porque Livewire busca por defecto el molde "layouts::app".
    - $menu: false para páginas que traen su propia barra (la home/landing).
    El JavaScript de Livewire lo agrega Livewire solo.
--}}
@props([
    'title' => null,
    'menu' => true,
])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ filled($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>
        @vite('resources/css/layout.css')
        {{ $head ?? '' }}
    </head>
    <body>
        @if ($menu)
        <nav class="menu">
            <a href="{{ route('home') }}" class="menu-marca">CV Belgrano</a>
            <a href="{{ route('crear') }}">Crear CV</a>

            @auth
                <span class="menu-usuario">Hola, {{ auth()->user()->name }}</span>

                {{-- Cerrar sesión es un POST (la ruta "logout" la trae Fortify) --}}
                <form method="POST" action="{{ route('logout') }}" class="menu-salir">
                    @csrf
                    <button type="submit">Cerrar sesión</button>
                </form>
            @else
                <a href="{{ route('login') }}">Iniciar sesión</a>
                <a href="{{ route('register') }}">Crear cuenta</a>
            @endauth
        </nav>
        @endif

        <main @class(['contenido' => $menu])>
            {{ $slot }}
        </main>
    </body>
</html>
