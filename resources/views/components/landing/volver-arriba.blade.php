{{--
    Botón flotante para volver al inicio de la landing.
    Queda fijo abajo a la derecha mientras se baja; aparece después de bajar un poco
    (eso lo hace el CSS, sin JavaScript).
--}}
@vite('resources/css/landing/volver-arriba.css')

@php($t = __('landing'))

<a href="#inicio" class="volver-arriba" aria-label="{{ $t['web']['arriba'] }}" title="{{ $t['web']['arriba'] }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
        <path d="M12 19V5" />
        <path d="M5.5 11.5L12 5l6.5 6.5" />
    </svg>
</a>
