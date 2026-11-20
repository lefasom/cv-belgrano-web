{{--
    PLANTILLA ATS (<x-cv.ats :cv="$cv" />)
    Pasada de App-de-cv/templates/TemplateATS.ts a Blade.

    ESTILO: una sola columna, Arial, sin foto, sin tablas ni iconos (lo lee bien un ATS).
    ORDEN FIJO: encabezado, perfil, experiencia, educación, habilidades.

    RECIBE $cv ya filtrado por App\Cv\DatosPlantilla: lo vacío no viene, así que
    no se muestra, no ocupa lugar y no aparece su título.

    CONTRATO CON resources/js/cv-paginado.js (lo cumplen todas las plantillas):
      - [data-cv-hoja]: el molde de UNA hoja A4, con un lugar por columna ([data-cv-slot]).
      - [data-cv-columna="x"]: el contenido de la columna x, que se reparte en las hojas.
      - [data-cv-seccion]: se reparte por ítems (su 1er hijo es el título).
      - [data-cv-campo]: el texto se puede editar desde el CV (ver App\Cv\BorradorCv::editar).
--}}
@props(['cv'])

@php
    // Atributos de un texto editable desde el CV.
    $editable = fn (string $campo) => new Illuminate\Support\HtmlString(
        'contenteditable="true" spellcheck="false" data-cv-campo="'.e($campo).'"'
    );

    $contacto = array_filter([
        'datos.email' => $cv['email'],
        'datos.phone' => $cv['telefono'],
        'datos.location' => $cv['ubicacion'],
    ]);
@endphp

{{-- ============ MOLDE DE HOJA ============ --}}
<div data-cv-hoja>
    <div class="cv-hoja cv-ats">
        <div class="cv-ats-contenido" data-cv-slot="principal"></div>
    </div>
</div>

<div data-cv-columna="principal">
    @if ($cv['vacio'])
        {{-- Solo en la vista previa: al imprimir se oculta --}}
        <p class="cv-vacio">{{ __('cv.creador.vacio') }}</p>
    @endif

    {{-- ============ ENCABEZADO ============ --}}
    @if ($cv['nombre'] || $cv['profesion'] || $contacto)
        <header class="cv-ats-encabezado">
            @if ($cv['nombre'])
                <h1 class="cv-ats-nombre" {{ $editable('datos.full_name') }}>{{ $cv['nombre'] }}</h1>
            @endif

            @if ($cv['profesion'])
                <p class="cv-ats-profesion" {{ $editable('datos.headline') }}>{{ $cv['profesion'] }}</p>
            @endif

            @if ($contacto)
                <p class="cv-ats-contacto">
                    {{-- En una sola línea: si no, el salto de línea agrega un espacio entre ítems --}}
                    @foreach ($contacto as $campo => $dato)@if (! $loop->first)<span class="cv-ats-sep">&nbsp;&middot;&nbsp;</span>@endif<span {{ $editable($campo) }}>{{ $dato }}</span>@endforeach
                </p>
            @endif
        </header>
    @endif

    {{-- ============ PERFIL PROFESIONAL ============ --}}
    @if ($cv['resumen'])
        <section class="cv-ats-seccion" data-cv-seccion>
            <h2 class="cv-ats-titulo">{{ __('cv.ats.perfil') }}</h2>
            <p class="cv-ats-parrafo" {{ $editable('datos.summary') }}>{{ $cv['resumen'] }}</p>
        </section>
    @endif

    {{-- ============ EXPERIENCIA LABORAL ============ --}}
    @if ($cv['experiencias'])
        <section class="cv-ats-seccion cv-ats-seccion-experiencia" data-cv-seccion>
            <h2 class="cv-ats-titulo">{{ __('cv.ats.experiencia') }}</h2>

            @foreach ($cv['experiencias'] as $e)
                <article class="cv-ats-item">
                    <p class="cv-ats-linea">
                        @if ($e['puesto'])<span class="cv-ats-puesto" {{ $editable("experiencias.{$e['i']}.position") }}>{{ $e['puesto'] }}</span>@endif
                        @if ($e['empresa'])@if ($e['puesto'])<span class="cv-ats-sep">&nbsp;—&nbsp;</span>@endif<span {{ $editable("experiencias.{$e['i']}.company_name") }}>{{ $e['empresa'] }}</span>@endif
                        @if ($e['periodo'])@if ($e['puesto'] || $e['empresa'])<span class="cv-ats-sep">&nbsp;·&nbsp;</span>@endif<span class="cv-ats-gris" {{ $editable("experiencias.{$e['i']}.period") }}>{{ $e['periodo'] }}</span>@endif
                    </p>

                    @if ($e['tareas'])
                        <ul class="cv-ats-tareas">
                            @foreach ($e['tareas'] as $j => $tarea)
                                <li {{ $editable("experiencias.{$e['i']}.description.{$j}") }}>{{ $tarea }}</li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        </section>
    @endif

    {{-- ============ EDUCACIÓN ============ --}}
    @if ($cv['estudios'])
        <section class="cv-ats-seccion cv-ats-seccion-lista" data-cv-seccion>
            <h2 class="cv-ats-titulo">{{ __('cv.ats.educacion') }}</h2>

            @foreach ($cv['estudios'] as $e)
                <p class="cv-ats-linea cv-ats-item">
                    @if ($e['titulo'])<span {{ $editable("estudios.{$e['i']}.title_obtained") }}>{{ $e['titulo'] }}</span>@endif
                    @if ($e['institucion'])@if ($e['titulo'])<span class="cv-ats-sep">&nbsp;—&nbsp;</span>@endif<span class="cv-ats-gris" {{ $editable("estudios.{$e['i']}.institution") }}>{{ $e['institucion'] }}</span>@endif
                    @if ($e['periodo'])@if ($e['titulo'] || $e['institucion'])<span class="cv-ats-sep">&nbsp;·&nbsp;</span>@endif<span class="cv-ats-gris" {{ $editable("estudios.{$e['i']}.period") }}>{{ $e['periodo'] }}</span>@endif
                    {{-- El estado no se edita acá: es una opción (Terminado / En curso) del modal --}}
                    @if ($e['estado'])<span class="cv-ats-sep">&nbsp;·&nbsp;</span><span class="cv-ats-gris">{{ $e['estado'] }}</span>@endif
                </p>
            @endforeach
        </section>
    @endif

    {{-- ============ HABILIDADES ============ --}}
    @if ($cv['habilidades'])
        <section class="cv-ats-seccion" data-cv-seccion>
            <h2 class="cv-ats-titulo">{{ __('cv.ats.habilidades') }}</h2>
            <p class="cv-ats-linea cv-ats-habilidades">
                {{-- En una sola línea: si no, el salto de línea agrega un espacio entre ítems --}}
                @foreach ($cv['habilidades'] as $h)@if (! $loop->first)<span class="cv-ats-sep">&nbsp;&middot;&nbsp;</span>@endif<span {{ $editable("habilidades.{$h['i']}.name") }}>{{ $h['nombre'] }}</span>@endforeach
            </p>
        </section>
    @endif
</div>
