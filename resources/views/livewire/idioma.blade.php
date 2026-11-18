<?php

use Livewire\Component;

new class extends Component
{
    /** Los idiomas de la web (como la lista IDIOMAS de la landing en React). */
    public array $idiomas = [
        'es' => ['corto' => 'ES', 'nombre' => 'Español'],
        'en' => ['corto' => 'EN', 'nombre' => 'English'],
        'pt' => ['corto' => 'PT', 'nombre' => 'Português'],
    ];

    /** ¿El menú de idiomas está abierto? (como un useState(false)) */
    public bool $abierto = false;

    /** La página donde está el selector, para volver a ella después de cambiar. */
    public string $volver = '';

    /** mount() corre una sola vez, al dibujar la página (como useEffect con []). */
    public function mount(): void
    {
        $this->volver = url()->current();
    }

    /** Abre o cierra el menú. */
    public function alternar(): void
    {
        $this->abierto = ! $this->abierto;
    }

    /** Guarda el idioma elegido en la sesión y vuelve a cargar la página en ese idioma. */
    public function cambiar(string $codigo): void
    {
        if (! array_key_exists($codigo, $this->idiomas)) {
            return;
        }

        session(['idioma' => $codigo]);

        $this->redirect($this->volver);
    }
};
?>

@php($actual = $idiomas[app()->getLocale()])

<div class="landing-idioma">
    <button
        type="button"
        class="landing-idioma-boton"
        aria-haspopup="true"
        aria-expanded="{{ $abierto ? 'true' : 'false' }}"
        aria-label="{{ __('landing.barra.idiomaAria') }}: {{ $actual['nombre'] }}"
        wire:click="alternar"
    >
        <x-landing.icono nombre="globo" />
        <span>{{ $actual['corto'] }}</span>
        <span class="landing-idioma-flecha" aria-hidden="true">▾</span>
    </button>

    @if ($abierto)
        <ul class="landing-idioma-menu">
            @foreach ($idiomas as $codigo => $idioma)
                <li wire:key="idioma-{{ $codigo }}">
                    <button
                        type="button"
                        lang="{{ $codigo }}"
                        @class(['activo' => $codigo === app()->getLocale()])
                        @if ($codigo === app()->getLocale()) aria-current="true" @endif
                        wire:click="cambiar('{{ $codigo }}')"
                    >
                        <span class="landing-idioma-codigo">{{ $idioma['corto'] }}</span>
                        {{ $idioma['nombre'] }}
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</div>
