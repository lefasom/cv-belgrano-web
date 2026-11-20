{{--
    PLANTILLA PERSONALIZADA "EJECUTIVO" (<x-cv.ejecutivo :cv="$cv" />)
    Pasada de App-de-cv/templates/administration/Template3.ts (la del 3.png) a Blade.

    ESTILO: columna lateral gris clara con títulos subrayados en dorado; nombre en
    mayúsculas con letra serif (Georgia), foto cuadrada a la derecha del nombre y
    títulos de sección en bandas azul marino. Georgia y Arial vienen con el sistema:
    no hace falta cargar letras de internet.

    COLUMNAS (como el DEFAULT_LAYOUT de la app):
      - lateral (gris):     contacto, educación
      - principal (blanca): nombre + foto, perfil, experiencia, habilidades

    Solo las secciones que hay en la web. Mismo contrato con cv-paginado.js que la
    plantilla ATS (ver ats.blade.php): cada columna se reparte en las hojas por separado.
--}}
@props(['cv'])

@php
    $editable = fn (string $campo) => new Illuminate\Support\HtmlString(
        'contenteditable="true" spellcheck="false" data-cv-campo="'.e($campo).'"'
    );

    $contacto = array_filter([
        'datos.phone' => $cv['telefono'],
        'datos.email' => $cv['email'],
        'datos.location' => $cv['ubicacion'],
    ]);
@endphp

{{-- ============ MOLDE DE HOJA: dos columnas ============ --}}
<div data-cv-hoja>
    <div class="cv-hoja cv-eje">
        <div class="cv-eje-lateral" data-cv-slot="lateral"></div>
        <div class="cv-eje-principal" data-cv-slot="principal"></div>
    </div>
</div>

{{-- ============ COLUMNA LATERAL (gris) ============ --}}
<div data-cv-columna="lateral">
    @if ($contacto)
        <section class="cv-eje-bloque" data-cv-seccion>
            <h3 class="cv-eje-titulo-lateral">{{ __('cv.secciones.contacto') }}</h3>

            @foreach ($contacto as $campo => $dato)
                <div class="cv-eje-contacto" {{ $editable($campo) }}>{{ $dato }}</div>
            @endforeach
        </section>
    @endif

    @if ($cv['estudios'])
        <section class="cv-eje-bloque" data-cv-seccion>
            <h3 class="cv-eje-titulo-lateral">{{ __('cv.secciones.educacion') }}</h3>

            @foreach ($cv['estudios'] as $e)
                <div class="cv-eje-estudio">
                    @if ($e['titulo'])
                        <div class="cv-eje-estudio-titulo" {{ $editable("estudios.{$e['i']}.title_obtained") }}>{{ $e['titulo'] }}</div>
                    @endif

                    @if ($e['institucion'] || $e['estado'] || $e['periodo'])
                        {{-- Orden de la app: institución • estado • período --}}
                        <div class="cv-eje-estudio-datos">
                            @if ($e['institucion'])<span {{ $editable("estudios.{$e['i']}.institution") }}>{{ $e['institucion'] }}</span>@endif
                            @if ($e['institucion'] && ($e['estado'] || $e['periodo'])) • @endif
                            @if ($e['estado'])<span>{{ $e['estado'] }}</span>@endif
                            @if ($e['estado'] && $e['periodo']) • @endif
                            @if ($e['periodo'])<span {{ $editable("estudios.{$e['i']}.period") }}>{{ $e['periodo'] }}</span>@endif
                        </div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif
</div>

{{-- ============ COLUMNA PRINCIPAL (blanca) ============ --}}
<div data-cv-columna="principal">
    @if ($cv['vacio'])
        <p class="cv-vacio">{{ __('cv.creador.vacio') }}</p>
    @endif

    {{-- Encabezado: nombre y profesión a la izquierda, foto a la derecha --}}
    @if ($cv['nombre'] || $cv['profesion'] || $cv['foto'])
        <header class="cv-eje-encabezado">
            <div class="cv-eje-nombre-bloque">
                @if ($cv['nombre'])
                    <h1 class="cv-eje-nombre" {{ $editable('datos.full_name') }}>{{ $cv['nombre'] }}</h1>
                @endif

                @if ($cv['profesion'])
                    <div class="cv-eje-profesion" {{ $editable('datos.headline') }}>{{ $cv['profesion'] }}</div>
                @endif
            </div>

            @if ($cv['foto'])
                <div class="cv-eje-foto">
                    <img src="{{ $cv['foto'] }}" alt="">
                </div>
            @endif
        </header>
    @endif

    @if ($cv['resumen'])
        <section class="cv-eje-bloque" data-cv-seccion>
            <h2 class="cv-eje-titulo">{{ __('cv.secciones.perfil') }}</h2>
            <p class="cv-eje-perfil" {{ $editable('datos.summary') }}>{{ $cv['resumen'] }}</p>
        </section>
    @endif

    @if ($cv['experiencias'])
        <section class="cv-eje-bloque" data-cv-seccion>
            <h2 class="cv-eje-titulo">{{ __('cv.secciones.experiencia') }}</h2>

            {{-- Como en el 3.png: empresa arriba; abajo, período a la izquierda y puesto a la derecha --}}
            @foreach ($cv['experiencias'] as $e)
                <div class="cv-eje-item">
                    @if ($e['empresa'])
                        <div class="cv-eje-empresa" {{ $editable("experiencias.{$e['i']}.company_name") }}>{{ $e['empresa'] }}</div>
                    @endif

                    @if ($e['periodo'] || $e['puesto'])
                        <div class="cv-eje-fila">
                            @if ($e['periodo'])
                                <div class="cv-eje-periodo" {{ $editable("experiencias.{$e['i']}.period") }}>{{ $e['periodo'] }}</div>
                            @endif
                            @if ($e['puesto'])
                                <div class="cv-eje-puesto" {{ $editable("experiencias.{$e['i']}.position") }}>{{ $e['puesto'] }}</div>
                            @endif
                        </div>
                    @endif

                    @if ($e['tareas'])
                        <div class="cv-eje-tareas">
                            @foreach ($e['tareas'] as $j => $tarea)
                                <div class="cv-eje-tarea" {{ $editable("experiencias.{$e['i']}.description.{$j}") }}>{{ $tarea }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    @if ($cv['habilidades'])
        <section class="cv-eje-bloque" data-cv-seccion>
            <h2 class="cv-eje-titulo">{{ __('cv.secciones.habilidades') }}</h2>

            <div class="cv-eje-chips">
                @foreach ($cv['habilidades'] as $h)
                    <div class="cv-eje-chip" {{ $editable("habilidades.{$h['i']}.name") }}>{{ $h['nombre'] }}</div>
                @endforeach
            </div>
        </section>
    @endif
</div>
