<?php

use App\Cv\BorradorCv;
use Livewire\Component;

new class extends Component
{
    /*
     * EDUCACIÓN: arriba del modal están "Educación 1, 2, 3… + Nueva".
     * Tocar una la carga en el formulario para editarla (o eliminarla).
     * "+ Nueva" deja el formulario vacío para agregar otra.
     */
    private const VACIO = [
        'title_obtained' => '',
        'institution' => '',
        'period' => '',
        'status' => 'Completed',
    ];

    /** Lo que se edita en el modal (como un useState local). */
    public array $borrador = [];

    /** Qué estudio se está editando: su número en la lista, o null si es uno nuevo. */
    public ?int $elegido = null;

    public bool $abierto = false;

    /** Al abrir empieza en "+ Nueva". */
    public function abrir(): void
    {
        $this->elegir(null);
        $this->abierto = true;
    }

    public function cerrar(): void
    {
        $this->abierto = false;
    }

    /** Carga en el formulario el estudio $indice (null = uno nuevo, vacío). */
    public function elegir(?int $indice): void
    {
        $lista = BorradorCv::lista('estudios');

        if ($indice !== null && ! isset($lista[$indice])) {
            $indice = null;
        }

        $this->elegido = $indice;
        $this->borrador = $indice === null ? self::VACIO : array_merge(self::VACIO, $lista[$indice]);
        $this->resetValidation();
    }

    /** Guardar: valida y agrega el nuevo, o reemplaza el que se estaba editando. */
    public function guardar(): void
    {
        $this->validate([
            'borrador.title_obtained' => 'required|string|max:255',
            'borrador.institution' => 'required|string|max:255',
            'borrador.period' => 'required|string|max:100',
            'borrador.status' => 'required|in:Completed,In Progress',
        ], [
            '*.required' => 'Completá este campo.',
            '*.max' => 'Es demasiado largo.',
            '*.in' => 'Elegí una opción de la lista.',
        ]);

        $estudio = array_intersect_key($this->borrador, self::VACIO);

        if ($this->elegido === null) {
            BorradorCv::agregar('estudios', $estudio);
        } else {
            BorradorCv::reemplazar('estudios', $this->elegido, $estudio);
        }

        $this->abierto = false;
        $this->dispatch('cv-actualizado');
    }

    /** Eliminar el estudio elegido. El modal queda abierto, en "+ Nueva". */
    public function eliminar(): void
    {
        if ($this->elegido !== null) {
            BorradorCv::quitar('estudios', $this->elegido);
            $this->dispatch('cv-actualizado');
        }

        $this->elegir(null);
    }
};
?>

<div class="creador-seccion">
    {{-- El CSS del botón y del modal (lo comparten las cuatro secciones) --}}
    @vite(['resources/css/creador/seccion.css', 'resources/css/creador/modal.css'])

    @php($cantidad = count(App\Cv\BorradorCv::lista('estudios')))

    <button type="button" class="creador-boton" wire:click="abrir">
        <span>{{ __('cv.creador.educacion') }}</span>
        @if ($cantidad)
            <span class="creador-boton-cuenta">{{ $cantidad }}</span>
        @endif
    </button>

    @if ($abierto)
        <div class="modal-fondo" wire:click="cerrar"></div>

        <div class="modal">
            <button type="button" class="modal-x" wire:click="cerrar" aria-label="Cerrar">×</button>

            <form wire:submit="guardar">
                <h2>{{ $elegido === null ? 'Agregar estudio' : 'Editar educación '.($elegido + 1) }}</h2>

                @if ($cantidad)
                    <x-creador.selector :cantidad="$cantidad" :elegido="$elegido" :etiqueta="__('cv.creador.educacion')" />
                @endif

                <label for="edu-title_obtained">Título</label>
                <input id="edu-title_obtained" type="text" wire:model="borrador.title_obtained" placeholder="Ej.: Técnico en programación">
                @error('borrador.title_obtained') <p class="error">{{ $message }}</p> @enderror

                <label for="edu-institution">Institución</label>
                <input id="edu-institution" type="text" wire:model="borrador.institution" placeholder="Ej.: UTN">
                @error('borrador.institution') <p class="error">{{ $message }}</p> @enderror

                <label for="edu-period">Período</label>
                <input id="edu-period" type="text" wire:model="borrador.period" placeholder="Ej.: 2018 - 2021">
                @error('borrador.period') <p class="error">{{ $message }}</p> @enderror

                {{-- En la base se guarda en inglés (como en la factory); al usuario se le muestra en español --}}
                <label for="edu-status">Estado</label>
                <select id="edu-status" wire:model="borrador.status">
                    <option value="Completed">Terminado</option>
                    <option value="In Progress">En curso</option>
                </select>
                @error('borrador.status') <p class="error">{{ $message }}</p> @enderror

                <div class="modal-botones">
                    @if ($elegido !== null)
                        {{-- wire:confirm pregunta antes de llamar a eliminar() --}}
                        <button type="button" class="modal-eliminar" wire:click="eliminar" wire:confirm="¿Eliminar la educación {{ $elegido + 1 }}?">{{ __('cv.creador.eliminar') }}</button>
                    @endif
                    <button type="button" class="boton-secundario" wire:click="cerrar">Cancelar</button>
                    <button type="submit" class="boton" wire:loading.attr="disabled" wire:target="guardar">
                        <span wire:loading.remove wire:target="guardar">Guardar</span>
                        <span wire:loading wire:target="guardar">Guardando…</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
