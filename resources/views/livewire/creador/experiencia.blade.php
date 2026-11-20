<?php

use App\Cv\BorradorCv;
use Livewire\Component;

new class extends Component
{
    /*
     * EXPERIENCIA: arriba del modal están "Experiencia 1, 2, 3… + Nueva".
     * Tocar una la carga en el formulario para editarla (o eliminarla).
     * "+ Nueva" deja el formulario vacío para agregar otra.
     */
    private const VACIA = [
        'company_name' => '',
        'position' => '',
        'period' => '',
        'description' => '',
    ];

    /** Lo que se edita en el modal (como un useState local). */
    public array $borrador = [];

    /** Qué experiencia se está editando: su número en la lista, o null si es una nueva. */
    public ?int $elegida = null;

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

    /** Carga en el formulario la experiencia $indice (null = una nueva, vacía). */
    public function elegir(?int $indice): void
    {
        $lista = BorradorCv::lista('experiencias');

        if ($indice !== null && ! isset($lista[$indice])) {
            $indice = null;
        }

        $this->elegida = $indice;
        $this->borrador = $indice === null ? self::VACIA : array_merge(self::VACIA, $lista[$indice]);
        $this->resetValidation();
    }

    /** Guardar: valida y agrega la nueva, o reemplaza la que se estaba editando. */
    public function guardar(): void
    {
        $this->validate([
            'borrador.company_name' => 'required|string|max:255',
            'borrador.position' => 'required|string|max:255',
            'borrador.period' => 'required|string|max:100',
            'borrador.description' => 'required|string|max:2000',
        ], [
            '*.required' => 'Completá este campo.',
            '*.max' => 'Es demasiado largo.',
        ]);

        $experiencia = array_intersect_key($this->borrador, self::VACIA);

        if ($this->elegida === null) {
            BorradorCv::agregar('experiencias', $experiencia);
        } else {
            BorradorCv::reemplazar('experiencias', $this->elegida, $experiencia);
        }

        $this->abierto = false;
        $this->dispatch('cv-actualizado');
    }

    /** Eliminar la experiencia elegida. El modal queda abierto, en "+ Nueva". */
    public function eliminar(): void
    {
        if ($this->elegida !== null) {
            BorradorCv::quitar('experiencias', $this->elegida);
            $this->dispatch('cv-actualizado');
        }

        $this->elegir(null);
    }
};
?>

<div class="creador-seccion">
    {{-- El CSS del botón y del modal (lo comparten las cuatro secciones) --}}
    @vite(['resources/css/creador/seccion.css', 'resources/css/creador/modal.css'])

    @php($cantidad = count(App\Cv\BorradorCv::lista('experiencias')))

    <button type="button" class="creador-boton" wire:click="abrir">
        <span>{{ __('cv.creador.experiencia') }}</span>
        @if ($cantidad)
            <span class="creador-boton-cuenta">{{ $cantidad }}</span>
        @endif
    </button>

    @if ($abierto)
        <div class="modal-fondo" wire:click="cerrar"></div>

        <div class="modal">
            <button type="button" class="modal-x" wire:click="cerrar" aria-label="Cerrar">×</button>

            <form wire:submit="guardar">
                <h2>{{ $elegida === null ? 'Agregar experiencia' : 'Editar experiencia '.($elegida + 1) }}</h2>

                @if ($cantidad)
                    <x-creador.selector :cantidad="$cantidad" :elegido="$elegida" :etiqueta="__('cv.creador.experiencia')" />
                @endif

                <label for="exp-position">Puesto</label>
                <input id="exp-position" type="text" wire:model="borrador.position" placeholder="Ej.: Desarrollador React Native">
                @error('borrador.position') <p class="error">{{ $message }}</p> @enderror

                <label for="exp-company_name">Empresa</label>
                <input id="exp-company_name" type="text" wire:model="borrador.company_name" placeholder="Ej.: FULLMED">
                @error('borrador.company_name') <p class="error">{{ $message }}</p> @enderror

                <label for="exp-period">Período</label>
                <input id="exp-period" type="text" wire:model="borrador.period" placeholder="Ej.: 2023 - actualidad">
                @error('borrador.period') <p class="error">{{ $message }}</p> @enderror

                <label for="exp-description">Qué hiciste <span class="modal-ayuda">(cada renglón es una viñeta)</span></label>
                <textarea id="exp-description" wire:model="borrador.description" rows="4" placeholder="Tareas y logros principales."></textarea>
                @error('borrador.description') <p class="error">{{ $message }}</p> @enderror

                <div class="modal-botones">
                    @if ($elegida !== null)
                        {{-- wire:confirm pregunta antes de llamar a eliminar() --}}
                        <button type="button" class="modal-eliminar" wire:click="eliminar" wire:confirm="¿Eliminar la experiencia {{ $elegida + 1 }}?">{{ __('cv.creador.eliminar') }}</button>
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
