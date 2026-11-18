{{-- Una captura de la app dentro del marco de celular (en React era <Captura>) --}}
@props([
    'archivo',
    'titulo',
    'detalle' => null,
])

<div class="landing-pantalla">
    <img
        src="{{ asset('images/landing/'.$archivo) }}"
        alt="{{ $titulo }}. {{ $detalle }}"
        width="724"
        height="1610"
        loading="lazy"
        decoding="async"
    >
</div>
