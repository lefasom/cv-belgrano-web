{{--
    SELECTOR DEL MODAL (<x-creador.selector :cantidad="3" :elegido="0" :etiqueta="'Educación'" />)
    La fila de arriba de los modales de Experiencia y Educación:
      [Educación 1] [Educación 2] [Educación 3] [+ Nueva]
    Tocar uno llama a elegir(i) del componente Livewire que lo usa (elegir(null) = nueva).
--}}
@props(['cantidad', 'elegido', 'etiqueta'])

@php($t = __('cv'))

<div class="modal-selector" role="tablist" aria-label="{{ $etiqueta }}">
    @for ($i = 0; $i < $cantidad; $i++)
        <button
            type="button"
            role="tab"
            aria-selected="{{ $elegido === $i ? 'true' : 'false' }}"
            @class(['modal-selector-item', 'activo' => $elegido === $i])
            wire:click="elegir({{ $i }})"
            wire:key="selector-{{ $i }}"
        >{{ $etiqueta }} {{ $i + 1 }}</button>
    @endfor

    <button
        type="button"
        role="tab"
        aria-selected="{{ $elegido === null ? 'true' : 'false' }}"
        @class(['modal-selector-item', 'modal-selector-nueva', 'activo' => $elegido === null])
        wire:click="elegir(null)"
    >+ {{ $t['creador']['nueva'] }}</button>
</div>
