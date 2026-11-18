{{--
    CREAR (/crear): el creador de CV online, sin cuenta.
    Cada sección es un componente Livewire con su propio estado y su propio modal.
--}}
<x-layouts::app title="Crear CV">
    <x-slot:head>
        @vite('resources/css/crear.css')
    </x-slot:head>

    <span class="etiqueta">CREADOR DE CV</span>
    <h1 class="titulo">Armá tu CV <span>gratis y en minutos</span></h1>
    <p class="bajada">Completá cada sección. Lo que guardás queda mientras navegás, aunque recargues la página.</p>

    <section class="secciones">
        <livewire:creador.datos />
        <livewire:creador.experiencia />
        <livewire:creador.educacion />
        <livewire:creador.habilidades />
    </section>
</x-layouts::app>
