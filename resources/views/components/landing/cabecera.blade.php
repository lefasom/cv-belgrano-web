{{-- Etiqueta + título + texto de una sección (en React era <Cabecera>) --}}
@props([
    'datos',
    'id',
    'clase' => 'landing-experiencia-cabecera',
])

<div class="{{ $clase }}">
    <span class="landing-etiqueta">{{ $datos['etiqueta'] }}</span>
    <h2 id="{{ $id }}">{{ $datos['titulo'] }}</h2>
    @if (! empty($datos['texto']))
        <p>{{ $datos['texto'] }}</p>
    @endif
</div>
