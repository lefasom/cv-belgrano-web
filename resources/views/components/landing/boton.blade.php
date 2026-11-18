{{--
    Botón grande de la landing (en React era <Boton>).
    - Con url: es un enlace. Si "externo" es true, abre en otra pestaña.
    - Sin url: se dibuja igual pero sin acción (por ejemplo, el APK que todavía no tiene enlace).
    El ícono entra por el slot con nombre "icono".
--}}
@props([
    'url' => null,
    'texto',
    'detalle' => null,
    'externo' => true,
])

@if ($url)
    <a href="{{ $url }}" {{ $attributes->merge(['class' => 'landing-boton']) }}
        @if ($externo) target="_blank" rel="noopener noreferrer" @endif>
        {{ $icono ?? '' }}
        <span>
            {{ $texto }}
            @if ($detalle) <small>{{ $detalle }}</small> @endif
        </span>
    </a>
@else
    <div {{ $attributes->merge(['class' => 'landing-boton landing-boton-pendiente']) }}>
        {{ $icono ?? '' }}
        <span>
            {{ $texto }}
            @if ($detalle) <small>{{ $detalle }}</small> @endif
        </span>
    </div>
@endif
