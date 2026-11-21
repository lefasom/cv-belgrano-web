<?php

use App\Cv\FotoCv;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    /*
     * FOTO DEL CV (solo la usa la plantilla Personalizada).
     *
     * 1. El <input type="file" wire:model="archivo"> sube la imagen a una carpeta
     *    temporal de Livewire (storage/app/private/livewire-tmp).
     * 2. updatedArchivo() la achica con GD (App\Cv\FotoCv), la borra del disco y
     *    manda la foto chica a la vista previa con un evento.
     *
     * NO se guarda: ni en la base de datos ni en la sesión. Al recargar la página, se pierde.
     */
    use WithFileUploads;

    /** La imagen recién subida (un archivo temporal). */
    public $archivo;

    /** ¿Hay una foto puesta? Cambia el texto del botón y muestra "Quitar". */
    public bool $hayFoto = false;

    /** updated + nombre de la propiedad: Livewire lo llama solo cuando termina la subida. */
    public function updatedArchivo(): void
    {
        $this->validate(
            ['archivo' => 'image|mimes:jpg,jpeg,png,webp|max:10240'],
            [
                'archivo.image' => __('cv')['creador']['fotoNoImagen'],
                'archivo.mimes' => __('cv')['creador']['fotoNoImagen'],
                'archivo.max' => __('cv')['creador']['fotoPesada'],
            ],
        );

        $foto = FotoCv::comprimir($this->archivo->getRealPath());

        // El original ya no hace falta: se borra de la carpeta temporal.
        $this->archivo->delete();
        $this->reset('archivo');

        if (! $foto) {
            $this->addError('archivo', __('cv')['creador']['fotoNoImagen']);

            return;
        }

        $this->hayFoto = true;
        $this->dispatch('foto-cambiada', foto: $foto);
    }

    public function quitar(): void
    {
        $this->hayFoto = false;
        $this->dispatch('foto-cambiada', foto: null);
    }
};
?>

@php($t = __('cv'))

<div class="creador-foto">
    {{-- El <label> abre el selector de archivos: el input queda escondido --}}
    <label class="creador-herramienta-boton" for="creador-foto-archivo">
        <span wire:loading.remove wire:target="archivo">
            {{ $hayFoto ? $t['creador']['fotoCambiar'] : $t['creador']['fotoAgregar'] }}
        </span>
        <span wire:loading wire:target="archivo">{{ $t['creador']['fotoSubiendo'] }}</span>
    </label>
    <input id="creador-foto-archivo" type="file" accept="image/jpeg,image/png,image/webp" wire:model="archivo" hidden>

    @if ($hayFoto)
        <button type="button" class="creador-herramienta-boton creador-herramienta-secundario" wire:click="quitar">
            {{ $t['creador']['fotoQuitar'] }}
        </button>
    @endif

    @error('archivo') <p class="error">{{ $message }}</p> @enderror
</div>
