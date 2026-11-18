{{-- Dos capturas a un lado y texto con lista al otro (en React era <SeccionTelefonos>) --}}
@props([
    'datos',
    'id',
])

<section class="landing-formularios" aria-labelledby="{{ $id }}">
    <div class="landing-formularios-telefonos">
        @foreach ($datos['capturas'] as $captura)
            <figure @class(['landing-formularios-item', 'landing-formularios-item-dos' => $loop->index === 1])>
                <x-landing.captura :archivo="$captura['archivo']" :titulo="$captura['titulo']" :detalle="$captura['detalle'] ?? null" />
                <figcaption>{{ $captura['pie'] ?? '' }}</figcaption>
            </figure>
        @endforeach
    </div>

    <div class="landing-formularios-texto">
        <span class="landing-etiqueta">{{ $datos['etiqueta'] }}</span>
        <h2 id="{{ $id }}">{{ $datos['titulo'] }}</h2>
        <p>{{ $datos['texto'] }}</p>
        <x-landing.lista :items="$datos['lista']" />
    </div>
</section>
