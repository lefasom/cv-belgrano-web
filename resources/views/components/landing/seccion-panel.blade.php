{{--
    Panel redondeado: texto a un lado y dos capturas al otro (en React era <SeccionPanel>).
    - Slot con nombre "etiqueta": reemplaza la etiqueta común (la usa la sección de IA).
    - Slot por defecto ($slot, como children): reemplaza la lista.
--}}
@props([
    'datos',
    'id',
    'idSeccion' => null,
    'clase' => '',
])

<section class="landing-oscuro {{ $clase }}" @if ($idSeccion) id="{{ $idSeccion }}" @endif aria-labelledby="{{ $id }}">
    <div class="landing-oscuro-texto">
        @if (isset($etiqueta))
            {{ $etiqueta }}
        @else
            <span class="landing-etiqueta">{{ $datos['etiqueta'] }}</span>
        @endif

        <h2 id="{{ $id }}">{{ $datos['titulo'] }}</h2>
        <p>{{ $datos['texto'] }}</p>

        @if ($slot->isNotEmpty())
            {{ $slot }}
        @else
            <x-landing.lista :items="$datos['lista']" />
        @endif

        @if (! empty($datos['nota']))
            <p class="landing-oscuro-nota">{{ $datos['nota'] }}</p>
        @endif
    </div>

    <div class="landing-oscuro-telefonos">
        @foreach ($datos['capturas'] as $captura)
            <figure class="landing-oscuro-item landing-oscuro-item-{{ $loop->first ? 'uno' : 'dos' }}">
                <x-landing.captura :archivo="$captura['archivo']" :titulo="$captura['titulo']" :detalle="$captura['detalle'] ?? null" />
                <figcaption>{{ $captura['pie'] ?? '' }}</figcaption>
            </figure>
        @endforeach
    </div>
</section>
