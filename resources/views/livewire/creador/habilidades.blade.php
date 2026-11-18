<?php

use Livewire\Attributes\Session;
use Livewire\Component;

new class extends Component
{
    /**
     * Lista de habilidades guardadas (sobrevive en la sesión).
     * Cada una tiene la columna `name` de la tabla `skills`.
     */
    #[Session]
    public array $habilidades = [];

    /** El borrador de la habilidad que se está cargando en el modal. */
    public array $borrador = [];

    public bool $abierto = false;

    public string $mensaje = '';

    public function abrir(): void
    {
        $this->borrador = ['name' => ''];
        $this->resetValidation();
        $this->mensaje = '';
        $this->abierto = true;
    }

    public function cerrar(): void
    {
        $this->abierto = false;
    }

    /** Guardar: valida y agrega la habilidad al final de la lista. */
    public function guardar(): void
    {
        $this->validate([
            'borrador.name' => 'required|string|max:100',
        ], [
            'borrador.name.required' => 'Escribí una habilidad.',
            'borrador.name.max' => 'Es demasiado largo.',
        ]);

        $this->habilidades[] = $this->borrador;
        $this->abierto = false;
        $this->mensaje = 'Habilidad guardada.';
    }
};
?>

<div class="seccion seccion-habilidades">
    {{-- El CSS de este componente: la tarjeta, el modal y lo propio de habilidades --}}
    @vite(['resources/css/creador/seccion.css', 'resources/css/creador/modal.css', 'resources/css/creador/habilidades.css'])

    <button type="button" class="boton" wire:click="abrir">Habilidades</button>

    @if ($mensaje)
        <p class="aviso">{{ $mensaje }}</p>
    @endif

    <ul class="habilidades-lista">
        @foreach ($habilidades as $i => $habilidad)
            <li wire:key="habilidad-{{ $i }}">{{ $habilidad['name'] }}</li>
        @endforeach
    </ul>

    @if (count($habilidades) === 0)
        <p class="seccion-vacia">Todavía no agregaste habilidades.</p>
    @endif

    @if ($abierto)
        <div class="modal-fondo" wire:click="cerrar"></div>

        <div class="modal">
            <button type="button" class="modal-x" wire:click="cerrar" aria-label="Cerrar">×</button>

            <form wire:submit="guardar">
                <h2>Agregar habilidad</h2>

                <label for="hab-name">Habilidad</label>
                <input id="hab-name" type="text" wire:model="borrador.name" placeholder="Ej.: Laravel">
                @error('borrador.name') <p class="error">{{ $message }}</p> @enderror

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
