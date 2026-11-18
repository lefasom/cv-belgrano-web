{{--
    Componente nuevo (no estaba en la landing de React):
    invita a probar el creador web y lleva a /crear. Tiene su propio CSS.
--}}
@vite('resources/css/landing/probar-online.css')

<section class="probar-online" id="probar" aria-labelledby="probar-online-titulo">
    <div class="probar-online-caja">
        <span class="landing-etiqueta">{{ __('landing.web.etiqueta') }}</span>
        <h2 id="probar-online-titulo">{{ __('landing.web.titulo') }} <span>{{ __('landing.web.resalte') }}</span></h2>
        <p>{{ __('landing.web.texto') }}</p>

        <x-landing.boton :url="route('crear')" :externo="false" :texto="__('landing.web.boton')" :detalle="__('landing.web.detalle')">
            <x-slot:icono><x-landing.icono nombre="lapiz" /></x-slot:icono>
        </x-landing.boton>
    </div>
</section>
