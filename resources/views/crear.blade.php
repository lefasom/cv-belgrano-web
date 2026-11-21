{{--
    CREAR (/crear): el creador de CV online, sin cuenta.
    - Izquierda (arriba en el celular): los cuatro botones que abren los modales
      y, debajo, las herramientas (foto, tamaño de letra y zoom).
    - Derecha (abajo en el celular): pestañas de plantilla y la vista previa del CV.
--}}
@php($t = __('cv'))
@php($l = __('layout'))

<x-layouts::app :title="$l['menu']['crear']">
    <x-slot:head>
        @vite('resources/css/crear.css')
    </x-slot:head>

    <div class="creador">
        <aside class="creador-panel">
            {{-- ============ SECCIONES: cada botón abre su modal ============ --}}
            <div class="creador-botones" role="group" aria-label="{{ $t['creador']['botonesAria'] }}">
                <livewire:creador.datos />
                <livewire:creador.experiencia />
                <livewire:creador.educacion />
                <livewire:creador.habilidades />
            </div>

            {{-- ============ HERRAMIENTAS ============ --}}
            <div class="creador-herramientas">
                <h2 class="creador-herramientas-titulo">{{ $t['creador']['herramientas'] }}</h2>

                <div class="creador-herramienta">
                    <span class="creador-herramienta-nombre">{{ $t['creador']['foto'] }}</span>
                    <livewire:creador.foto />
                    <p class="creador-ayuda">{{ $t['creador']['fotoAyuda'] }}</p>
                </div>

                {{-- Estos botones los maneja resources/js/cv-paginado.js (data-cv-accion) --}}
                <div class="creador-herramienta">
                    <span class="creador-herramienta-nombre" id="creador-letra">{{ $t['creador']['letra'] }}</span>
                    <div class="creador-control" role="group" aria-labelledby="creador-letra">
                        <button type="button" data-cv-accion="letra-menos" aria-label="{{ $t['creador']['letraMenos'] }}">A−</button>
                        <button type="button" data-cv-accion="letra-mas" aria-label="{{ $t['creador']['letraMas'] }}">A+</button>
                    </div>
                </div>

                {{-- Qué hace Ctrl + clic arrastrando sobre el CV: zoom (lupa) o desplazar (mano) --}}
                <div class="creador-herramienta">
                    <span class="creador-herramienta-nombre" id="creador-modo">{{ $t['creador']['mover'] }}</span>
                    <div class="creador-control" role="group" aria-labelledby="creador-modo">
                        <button type="button" class="creador-modo activo" data-cv-modo="zoom" aria-pressed="true">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.5 15.5 21 21M10.5 7.5v6M7.5 10.5h6"/></svg>
                            <span>{{ $t['creador']['zoom'] }}</span>
                        </button>
                        <button type="button" class="creador-modo" data-cv-modo="desplazar" aria-pressed="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 13V5.5a1.5 1.5 0 0 1 3 0V12m0-1V4.5a1.5 1.5 0 0 1 3 0V12m0-6.5a1.5 1.5 0 0 1 3 0V13m0-5.5a1.5 1.5 0 0 1 3 0V15a6 6 0 0 1-6 6h-1.5a6 6 0 0 1-4.6-2.2L4.6 15.3a1.6 1.6 0 0 1 2.4-2.1L8 14.5"/></svg>
                            <span>{{ $t['creador']['desplazar'] }}</span>
                        </button>
                        <button type="button" class="creador-control-valor" data-cv-accion="zoom-normal" data-cv-zoom-valor aria-label="{{ $t['creador']['zoomNormal'] }}">100%</button>
                    </div>
                    <p class="creador-ayuda">{{ $t['creador']['moverAyuda'] }}</p>
                </div>
            </div>
        </aside>

        <livewire:creador.vista-previa />
    </div>
</x-layouts::app>
