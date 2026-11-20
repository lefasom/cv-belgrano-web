{{--
    El molde de TODAS las páginas (home, Mis perfiles, login, registro…).
    Blade simple, sin estado y sin librerías de estilos.
    - {{ $slot }}: el contenido de cada página (como children en React).
    - {{ $head }}: algo extra para el <head>, por ejemplo el CSS de esa página.
    - $title: el título de la pestaña (opcional).
    - $menu: qué barra superior mostrar.
        true  (por defecto) = barra de la app: "Crear CV" y la cuenta (login / cerrar sesión).
        false = barra de la landing: links a las secciones, idioma y botones de descarga.
    Se usa con <x-layouts::app> en páginas Blade. Las páginas Livewire (Route::livewire)
    lo usan solas, porque Livewire busca por defecto el molde "layouts::app".
    El JavaScript de Livewire lo agrega Livewire solo.
--}}
@props([
    'title' => null,
    'menu' => true,
])
{{-- $t = textos de la landing (lang/*/landing.php), $l = textos del menú de la app (lang/*/layout.php) --}}
@php($t = __('landing'))
@php($l = __('layout.menu'))

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ filled($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>
        @vite(['resources/css/layout.css', 'resources/css/header.css'])
        {{ $head ?? '' }}
    </head>
    <body>
        <header @class(['landing-barra', 'landing-barra-app' => $menu])>
            {{-- Marca: siempre lleva al home --}}
            <a href="{{ route('home') }}" class="landing-barra-marca">
                <img src="{{ asset('images/landing/cvbelgrano.png') }}" alt="" width="36" height="36">
                <span>{{ $t['hero']['nombre'] }}</span>
            </a>

            {{-- Navegación: solo en la landing (links a sus secciones).
                 En la app no hace falta: la marca ya lleva al inicio. --}}
            @unless ($menu)
                <nav class="landing-barra-enlaces" aria-label="{{ $t['barra']['enlacesAria'] }}">
                    @foreach ($t['barra']['enlaces'] as $enlace)
                        <a href="{{ $enlace['href'] }}">{{ $enlace['texto'] }}</a>
                    @endforeach
                </nav>
            @endunless

            {{-- Acciones: controles, cuenta y botones destacados --}}
            <div class="landing-barra-acciones">
                @if ($menu)
                    @auth
                        {{-- :nombre en el texto se reemplaza por el nombre del usuario --}}
                        <span class="landing-barra-usuario">{{ __('layout.menu.hola', ['nombre' => auth()->user()->name]) }}</span>

                        {{-- Cerrar sesión es un POST (la ruta "logout" la trae Fortify) --}}
                        <form method="POST" action="{{ route('logout') }}" class="landing-barra-salir">
                            @csrf
                            <button type="submit">{{ $l['salir'] }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="landing-barra-link">{{ $l['entrar'] }}</a>
                        <a href="{{ route('register') }}" class="landing-barra-boton">{{ $l['registrarse'] }}</a>
                    @endauth
                @else
                    {{-- Selector de idioma: componente Livewire con su propio estado --}}
                    <livewire:idioma />
                    <a href="{{ route('crear') }}" class="landing-barra-boton landing-barra-boton-online">{{ $t['web']['crear'] }}</a>
                    <a href="#descargar" class="landing-barra-boton">{{ $t['barra']['descargar'] }}</a>
                @endif
            </div>
        </header>

        <main @class(['contenido' => $menu])>
            {{ $slot }}
        </main>
    </body>
</html>
