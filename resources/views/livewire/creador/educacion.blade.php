<?php

use Livewire\Attributes\Session;
use Livewire\Component;

new class extends Component
{
    /**
     * Lista de estudios guardados (sobrevive en la sesión).
     * Cada uno tiene las columnas de la tabla `education`.
     */
    #[Session]
    public array $estudios = [];

    /** El borrador del estudio que se está cargando en el modal. */
    public array $borrador = [];

    public bool $abierto = false;

    public string $mensaje = '';

    public function abrir(): void
    {
        $this->borrador = [
            'title_obtained' => '',
            'institution' => '',
            'period' => '',
            'status' => 'Completed',
        ];
        $this->resetValidation();
        $this->mensaje = '';
        $this->abierto = true;
    }

    public function cerrar(): void
    {
        $this->abierto = false;
    }

    /** Guardar: valida y agrega el estudio al final de la lista. */
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

        $this->estudios[] = $this->borrador;
        $this->abierto = false;
        $this->mensaje = 'Estudio guardado.';
    }
};
?>

<div class="seccion seccion-educacion">
    {{-- El CSS de este componente: la tarjeta, el modal y lo propio de educacion --}}
    @vite(['resources/css/creador/seccion.css', 'resources/css/creador/modal.css', 'resources/css/creador/educacion.css'])

    <button type="button" class="boton" wire:click="abrir">Educación</button>

    @if ($mensaje)
        <p class="aviso">{{ $mensaje }}</p>
    @endif

    <ul class="educacion-lista">
        @foreach ($estudios as $i => $estudio)
            <li wire:key="estudio-{{ $i }}">
                <strong>{{ $estudio['title_obtained'] }}</strong>
                <span>{{ $estudio['institution'] }} · {{ $estudio['period'] }}</span>
                <span class="educacion-estado">{{ $estudio['status'] === 'Completed' ? 'Terminado' : 'En curso' }}</span>
            </li>
        @endforeach
    </ul>

    @if (count($estudios) === 0)
        <p class="seccion-vacia">Todavía no agregaste estudios.</p>
    @endif

    @if ($abierto)
        <div class="modal-fondo" wire:click="cerrar"></div>

        <div class="modal">
            <button type="button" class="modal-x" wire:click="cerrar" aria-label="Cerrar">×</button>

            <form wire:submit="guardar">
                <h2>Agregar estudio</h2>

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
