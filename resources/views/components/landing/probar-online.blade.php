{{--
    Componente nuevo (no estaba en la landing de React):
    invita a probar el creador web y lleva a /crear. Tiene su propio CSS.
--}}
@vite('resources/css/landing/probar-online.css')

@php($t = __('landing'))

<section class="probar-online" id="probar" aria-labelledby="probar-online-titulo">
    <div class="probar-online-caja">
        <span class="landing-etiqueta">{{ $t['web']['etiqueta'] }}</span>
        <h2 id="probar-online-titulo">{{ $t['web']['titulo'] }} <span>{{ $t['web']['resalte'] }}</span></h2>
        <p>{{ $t['web']['texto'] }}</p>

        <x-landing.boton :url="route('crear')" :externo="false" :texto="$t['web']['boton']" :detalle="$t['web']['detalle']">
            <x-slot:icono><x-landing.icono nombre="lapiz" /></x-slot:icono>
        </x-landing.boton>
    </div>
</section>
