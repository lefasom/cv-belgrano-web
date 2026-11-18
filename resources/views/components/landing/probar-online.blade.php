{{--
    Componente nuevo (no estaba en la landing de React):
    invita a probar el creador web y lleva a /crear. Tiene su propio CSS.
--}}
@vite('resources/css/landing/probar-online.css')

<section class="probar-online" id="probar" aria-labelledby="probar-online-titulo">
    <div class="probar-online-caja">
        <span class="landing-etiqueta">SIN INSTALAR NADA</span>
        <h2 id="probar-online-titulo">Probá crear tu CV ahora, <span>online y gratis.</span></h2>
        <p>
            Completá tus datos, tu experiencia, tus estudios y tus habilidades desde el navegador.
            No hace falta crear una cuenta.
        </p>

        <x-landing.boton :url="route('crear')" :externo="false" texto="Crear mi CV online" detalle="Gratis, desde cualquier dispositivo">
            <x-slot:icono><x-landing.icono nombre="lapiz" /></x-slot:icono>
        </x-landing.boton>
    </div>
</section>
