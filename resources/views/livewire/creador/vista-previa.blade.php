<?php

use App\Cv\BorradorCv;
use App\Cv\DatosPlantilla;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    /** Las plantillas que hay: clave => componente Blade (resources/views/components/cv/). */
    public const PLANTILLAS = [
        'ats' => 'cv.ats',
        'personalizada' => 'cv.ejecutivo',
    ];

    /** La plantilla elegida en las pestañas. */
    public string $plantilla = 'ats';

    /**
     * La foto achicada (data:image/jpeg;base64,...). La manda el componente "foto".
     * No tiene #[Session]: vive solo mientras la página está abierta.
     */
    public ?string $foto = null;

    /** El CV listo para la plantilla (lee la sesión y filtra lo vacío). */
    #[Computed]
    public function cv(): array
    {
        return DatosPlantilla::desde(BorradorCv::todo(), $this->foto);
    }

    /** Un modal guardó algo: se vuelve a leer la sesión y se redibuja. */
    #[On('cv-actualizado')]
    public function actualizar(): void
    {
        unset($this->cv);
    }

    #[On('foto-cambiada')]
    public function ponerFoto(?string $foto = null): void
    {
        // Solo se acepta lo que arma FotoCv: un JPEG en base64 y chico.
        $valida = $foto === null
            || (str_starts_with($foto, 'data:image/jpeg;base64,') && strlen($foto) < 400_000);

        if ($valida) {
            $this->foto = $foto;
            unset($this->cv);
        }
    }

    public function elegirPlantilla(string $plantilla): void
    {
        if (array_key_exists($plantilla, self::PLANTILLAS)) {
            $this->plantilla = $plantilla;
        }
    }

    /**
     * Edición desde el CV: cv-paginado.js lo llama al salir de un texto editado.
     * BorradorCv decide si el campo existe y si el texto vale; si no, no cambia nada.
     */
    public function editar(string $campo, string $texto): void
    {
        if (BorradorCv::editar($campo, $texto)) {
            unset($this->cv);
        }
    }
};
?>

@php($t = __('cv'))

<div class="creador-vista" data-cv-vista>
    @vite(['resources/css/cv/hojas.css', 'resources/css/cv/ats.css', 'resources/css/cv/ejecutivo.css', 'resources/js/cv-paginado.js'])

    {{-- ============ PESTAÑAS: qué plantilla usar ============ --}}
    <div class="creador-plantillas" role="tablist" aria-label="{{ $t['creador']['plantillasAria'] }}">
        @foreach (['ats' => $t['creador']['plantillaAts'], 'personalizada' => $t['creador']['plantillaPersonalizada']] as $clave => $nombre)
            <button
                type="button"
                role="tab"
                aria-selected="{{ $plantilla === $clave ? 'true' : 'false' }}"
                @class(['creador-plantilla', 'activa' => $plantilla === $clave])
                wire:click="elegirPlantilla('{{ $clave }}')"
            >{{ $nombre }}</button>
        @endforeach
    </div>

    {{-- El CV armado por PHP. Oculto: cv-paginado.js lo copia a las hojas A4.
         El wire:key va en el div de ADENTRO: al cambiar de plantilla Livewire reemplaza
         ese div entero, y data-cv-fuente (el que vigila el JavaScript) sigue siendo el mismo. --}}
    <div data-cv-fuente hidden>
        <div wire:key="plantilla-{{ $plantilla }}">
            <x-dynamic-component :component="$this::PLANTILLAS[$plantilla]" :cv="$this->cv" />
        </div>
    </div>

    {{-- Ventana de 90vh con scroll (y zoom); adentro, las hojas.
         wire:ignore: Livewire no las toca, las maneja el JavaScript --}}
    <div class="creador-hojas" data-cv-ventana aria-label="{{ $t['creador']['vistaAria'] }}">
        <div class="cv-marco" data-cv-marco wire:ignore>
            <div class="cv-paginas" data-cv-paginas></div>
        </div>
    </div>

    <div class="creador-pie">
        <p class="creador-ayuda">{{ $t['creador']['editarAyuda'] }}</p>
        <button type="button" class="boton" data-cv-accion="descargar">{{ $t['creador']['descargar'] }}</button>
    </div>
</div>
