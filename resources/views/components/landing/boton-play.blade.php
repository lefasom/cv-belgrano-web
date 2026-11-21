{{--
    Botón de Google Play (en React era <BotonPlay>).
    Los textos los lee de lang/*/landing.php con $t = __('landing'): no hace falta pasarlos por props,
    porque las traducciones están disponibles en cualquier vista.
--}}
@props([
    'claro' => false,
    'texto' => null,
])

@php($t = __('landing'))

@php($urlGooglePlay = 'https://play.google.com/store/apps/details?id=com.cvbelgrano.app')

<x-landing.boton
    :url="$urlGooglePlay"
    :texto="$texto ?? $t['botones']['play']"
    :detalle="$t['botones']['playDetalle']"
    :class="$claro ? 'landing-boton-claro' : ''"
>
    <x-slot:icono><x-landing.icono nombre="tienda" /></x-slot:icono>
</x-landing.boton>
