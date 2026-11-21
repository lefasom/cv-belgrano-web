<?php

use App\Cv\BorradorCv;
use Livewire\Component;

new class extends Component
{
    /*
     * HABILIDADES: como SeccionHabilidadesVista.tsx de la app.
     * El modal muestra TODAS las habilidades, cada una con una ✕ para eliminarla,
     * y abajo un campo para agregar otra. No se editan: se eliminan y se agregan.
     * Agregar no cierra el modal, así se pueden cargar varias seguidas.
     */

    /** La habilidad que se está escribiendo. */
    public string $nueva = '';

    public bool $abierto = false;

    public function abrir(): void
    {
        $this->nueva = '';
        $this->resetValidation();
        $this->abierto = true;
    }

    public function cerrar(): void
    {
        $this->abierto = false;
    }

    public function agregar(): void
    {
        $this->nueva = trim($this->nueva);

        $this->validate(
            ['nueva' => 'required|string|max:100'],
            ['nueva.required' => 'Escribí una habilidad.', 'nueva.max' => 'Es demasiado largo.'],
        );

        BorradorCv::agregar('habilidades', ['name' => $this->nueva]);
        $this->nueva = '';
        $this->dispatch('cv-actualizado');
    }

    public function eliminar(int $indice): void
    {
        BorradorCv::quitar('habilidades', $indice);
        $this->dispatch('cv-actualizado');
    }
};
?>

@php($t = __('cv'))

<div class="creador-seccion">
    {{-- El CSS del botón y del modal (lo comparten las cuatro secciones) --}}
    @vite(['resources/css/creador/seccion.css', 'resources/css/creador/modal.css'])

    @php($habilidades = App\Cv\BorradorCv::lista('habilidades'))

    <button type="button" class="creador-boton" wire:click="abrir">
        <span>{{ $t['creador']['habilidades'] }}</span>
        @if (count($habilidades))
            <span class="creador-boton-cuenta">{{ count($habilidades) }}</span>
        @endif
    </button>

    @if ($abierto)
        <div class="modal-fondo" wire:click="cerrar"></div>

        <div class="modal">
            <button type="button" class="modal-x" wire:click="cerrar" aria-label="Cerrar">×</button>

            <h2>{{ $t['creador']['habilidades'] }}</h2>

            {{-- Todas las habilidades, cada una con su ✕ --}}
            @if (count($habilidades))
                <ul class="modal-lista">
                    @foreach ($habilidades as $i => $habilidad)
                        <li wire:key="habilidad-{{ $i }}-{{ md5($habilidad['name'] ?? '') }}">
                            <span>{{ $habilidad['name'] ?? '' }}</span>
                            <button
                                type="button"
                                class="modal-lista-quitar"
                                wire:click="eliminar({{ $i }})"
                                aria-label="{{ $t['creador']['eliminar'] }}: {{ $habilidad['name'] ?? '' }}"
                            >×</button>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="modal-vacio">Todavía no agregaste habilidades.</p>
            @endif

            {{-- Agregar otra (Enter también agrega) --}}
            <form class="modal-agregar" wire:submit="agregar">
                <label for="hab-nueva">Nueva habilidad</label>
                <div class="modal-agregar-fila">
                    <input id="hab-nueva" type="text" wire:model="nueva" placeholder="Ej.: Laravel" autocomplete="off">
                    <button type="submit" class="boton" wire:loading.attr="disabled" wire:target="agregar">{{ $t['creador']['agregar'] }}</button>
                </div>
                @error('nueva') <p class="error">{{ $message }}</p> @enderror
            </form>

            <div class="modal-botones">
                <button type="button" class="boton" wire:click="cerrar">{{ $t['creador']['listo'] }}</button>
            </div>
        </div>
    @endif
</div>
