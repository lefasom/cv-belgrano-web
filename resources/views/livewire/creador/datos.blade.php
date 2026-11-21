<?php

use App\Cv\BorradorCv;
use Livewire\Component;

new class extends Component
{
    /** El borrador: lo que se edita dentro del modal (como un useState local). */
    public array $borrador = [];

    /** ¿El modal está abierto? El modal se muestra con un @if en la vista. */
    public bool $abierto = false;

    /** Al abrir: copia lo guardado (en la sesión) al borrador. Así Cancelar o la X no cambian nada. */
    public function abrir(): void
    {
        $this->borrador = BorradorCv::datos();
        $this->resetValidation();
        $this->abierto = true;
    }

    /** Cancelar o X: cierra sin tocar lo guardado. */
    public function cerrar(): void
    {
        $this->abierto = false;
    }

    /** Guardar: valida, guarda en la sesión, cierra el modal y avisa a la vista previa. */
    public function guardar(): void
    {
        $this->validate([
            'borrador.full_name' => 'required|string|max:255',
            'borrador.headline' => 'nullable|string|max:255',
            'borrador.email' => 'required|email|max:255',
            'borrador.phone' => 'nullable|string|max:50',
            'borrador.location' => 'nullable|string|max:255',
            'borrador.summary' => 'nullable|string|max:2000',
        ], [
            'borrador.full_name.required' => 'Poné tu nombre completo.',
            'borrador.email.required' => 'Poné tu correo.',
            'borrador.email.email' => 'El correo no es válido.',
            '*.max' => 'Es demasiado largo.',
        ]);

        BorradorCv::guardarDatos($this->borrador);
        $this->abierto = false;
        $this->dispatch('cv-actualizado');
    }
};
?>

@php($t = __('cv'))

<div class="creador-seccion">
    {{-- El CSS del botón y del modal (lo comparten las cuatro secciones) --}}
    @vite(['resources/css/creador/seccion.css', 'resources/css/creador/modal.css'])

    <button type="button" class="creador-boton" wire:click="abrir">
        <span>{{ $t['creador']['datos'] }}</span>
        @if (filled(App\Cv\BorradorCv::datos()['full_name']))
            <span class="creador-boton-cuenta" aria-hidden="true">✓</span>
        @endif
    </button>

    @if ($abierto)
        {{-- Fondo oscuro: tocarlo también cierra sin guardar --}}
        <div class="modal-fondo" wire:click="cerrar"></div>

        <div class="modal">
            <button type="button" class="modal-x" wire:click="cerrar" aria-label="Cerrar">×</button>

            <form wire:submit="guardar">
                <h2>Datos personales</h2>

                <label for="datos-full_name">Nombre completo</label>
                <input id="datos-full_name" type="text" wire:model="borrador.full_name" placeholder="Ej.: Ana López">
                @error('borrador.full_name') <p class="error">{{ $message }}</p> @enderror

                <label for="datos-headline">Profesión</label>
                <input id="datos-headline" type="text" wire:model="borrador.headline" placeholder="Ej.: Desarrolladora web">
                @error('borrador.headline') <p class="error">{{ $message }}</p> @enderror

                <label for="datos-email">Correo</label>
                <input id="datos-email" type="email" wire:model="borrador.email" placeholder="Ej.: ana@correo.com">
                @error('borrador.email') <p class="error">{{ $message }}</p> @enderror

                <label for="datos-phone">Teléfono</label>
                <input id="datos-phone" type="text" wire:model="borrador.phone" placeholder="Ej.: 11 2345-6789">
                @error('borrador.phone') <p class="error">{{ $message }}</p> @enderror

                <label for="datos-location">Ubicación</label>
                <input id="datos-location" type="text" wire:model="borrador.location" placeholder="Ej.: Buenos Aires">
                @error('borrador.location') <p class="error">{{ $message }}</p> @enderror

                <label for="datos-summary">Resumen</label>
                <textarea id="datos-summary" wire:model="borrador.summary" rows="3" placeholder="Contá en pocas líneas quién sos y qué buscás."></textarea>
                @error('borrador.summary') <p class="error">{{ $message }}</p> @enderror

                <div class="modal-botones">
                    <button type="button" class="boton-secundario" wire:click="cerrar">Cancelar</button>
                    {{-- wire:loading muestra u oculta cosas mientras corre guardar() en el servidor --}}
                    <button type="submit" class="boton" wire:loading.attr="disabled" wire:target="guardar">
                        <span wire:loading.remove wire:target="guardar">Guardar</span>
                        <span wire:loading wire:target="guardar">Guardando…</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
