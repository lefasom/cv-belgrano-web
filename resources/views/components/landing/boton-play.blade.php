{{--
    Botón de Google Play (en React era <BotonPlay>).
    Los textos los lee de lang/es/landing.php con __(): no hace falta pasarlos por props,
    porque las traducciones están disponibles en cualquier vista.
--}}
@props([
    'claro' => false,
    'texto' => null,
])

<x-landing.boton
    :url="config('cvbelgrano.url_google_play')"
    :texto="$texto ?? __('landing.botones.play')"
    :detalle="__('landing.botones.playDetalle')"
    :class="$claro ? 'landing-boton-claro' : ''"
>
    <x-slot:icono><x-landing.icono nombre="tienda" /></x-slot:icono>
</x-landing.boton>
