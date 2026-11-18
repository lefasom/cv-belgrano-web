<?php

use Livewire\Attributes\Session;
use Livewire\Component;

new class extends Component
{
    /**
     * Lista de experiencias guardadas (sobrevive en la sesión).
     * Cada una tiene las columnas de la tabla `experiences`.
     */
    #[Session]
    public array $experiencias = [];

    /** El borrador de la experiencia que se está cargando en el modal. */
    public array $borrador = [];

    public bool $abierto = false;

    public string $mensaje = '';

    public function abrir(): void
    {
        $this->borrador = [
            'company_name' => '',
            'position' => '',
            'period' => '',
            'description' => '',
        ];
        $this->resetValidation();
        $this->mensaje = '';
        $this->abierto = true;
    }

    public function cerrar(): void
    {
        $this->abierto = false;
    }

    /** Guardar: valida y agrega la experiencia al final de la lista. */
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

        $this->experiencias[] = $this->borrador;
        $this->abierto = false;
        $this->mensaje = 'Experiencia guardada.';
    }
};
?>

<div class="seccion seccion-experiencia">
    {{-- El CSS de este componente: la tarjeta, el modal y lo propio de experiencia --}}
    @vite(['resources/css/creador/seccion.css', 'resources/css/creador/modal.css', 'resources/css/creador/experiencia.css'])

    <button type="button" class="boton" wire:click="abrir">Experiencia</button>

    @if ($mensaje)
        <p class="aviso">{{ $mensaje }}</p>
    @endif

    <ul class="experiencia-lista">
        @foreach ($experiencias as $i => $experiencia)
            <li wire:key="experiencia-{{ $i }}">
                <strong>{{ $experiencia['position'] }}</strong>
                <span>{{ $experiencia['company_name'] }} · {{ $experiencia['period'] }}</span>
            </li>
        @endforeach
    </ul>

    @if (count($experiencias) === 0)
        <p class="seccion-vacia">Todavía no agregaste experiencias.</p>
    @endif

    @if ($abierto)
        <div class="modal-fondo" wire:click="cerrar"></div>

        <div class="modal">
            <button type="button" class="modal-x" wire:click="cerrar" aria-label="Cerrar">×</button>

            <form wire:submit="guardar">
                <h2>Agregar experiencia</h2>

                <label for="exp-position">Puesto</label>
                <input id="exp-position" type="text" wire:model="borrador.position" placeholder="Ej.: Desarrollador React Native">
                @error('borrador.position') <p class="error">{{ $message }}</p> @enderror

                <label for="exp-company_name">Empresa</label>
                <input id="exp-company_name" type="text" wire:model="borrador.company_name" placeholder="Ej.: FULLMED">
                @error('borrador.company_name') <p class="error">{{ $message }}</p> @enderror

                <label for="exp-period">Período</label>
                <input id="exp-period" type="text" wire:model="borrador.period" placeholder="Ej.: 2023 - actualidad">
                @error('borrador.period') <p class="error">{{ $message }}</p> @enderror

                <label for="exp-description">Qué hiciste</label>
                <textarea id="exp-description" wire:model="borrador.description" rows="3" placeholder="Tareas y logros principales."></textarea>
                @error('borrador.description') <p class="error">{{ $message }}</p> @enderror

                <div class="modal-botones">
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
