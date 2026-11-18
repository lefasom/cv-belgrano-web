
@php($t = __('landing'))
{{-- PEGAR AQUÍ EL ENLACE REAL DE DESCARGA DEL APK (vacío = el botón se ve sin acción) --}}
@php($urlApk = '')

<x-layouts::app :title="$t['meta']['titulo']" :menu="false">
    <x-slot:head>
        @vite('resources/css/home.css')
    </x-slot:head>

    <div class="landing-page">

        {{-- ============ BARRA SUPERIOR ============ --}}

        <header class="landing-barra">
            <a href="#inicio" class="landing-barra-marca">
                <img src="{{ asset('images/landing/cvbelgrano.png') }}" alt="" width="36" height="36">
                <span>{{ $t['hero']['nombre'] }}</span>
            </a>
            <nav class="landing-barra-enlaces" aria-label="{{ $t['barra']['enlacesAria'] }}">
                @foreach ($t['barra']['enlaces'] as $enlace)
                    <a href="{{ $enlace['href'] }}">{{ $enlace['texto'] }}</a>
                @endforeach
            </nav>
            <div class="landing-barra-acciones">
                {{-- Selector de idioma: componente Livewire con su propio estado --}}
                <livewire:idioma />
                <a href="{{ route('crear') }}" class="landing-barra-boton landing-barra-boton-online">{{ $t['web']['crear'] }}</a>
                <a href="#descargar" class="landing-barra-boton">{{ $t['barra']['descargar'] }}</a>
            </div>
        </header>

        {{-- ============ HERO ============ --}}

        <section class="landing-hero" id="inicio">
            <div class="landing-contenido">
                <div class="landing-identidad">
                    <img src="{{ asset('images/landing/cvbelgrano.png') }}" alt="{{ $t['hero']['logoAlt'] }}" class="landing-logo">
                    <div>
                        <h1>{{ $t['hero']['nombre'] }}</h1>
                        <p>{{ $t['hero']['subtitulo'] }}</p>
                    </div>
                </div>

                <div class="landing-texto">
                    <span class="landing-etiqueta">{{ $t['hero']['etiqueta'] }}</span>
                    <h2>
                        {{ $t['hero']['titulo'] }}
                        <span> {{ $t['hero']['tituloResalte'] }}</span>
                    </h2>
                    <p>{{ $t['hero']['texto'] }}</p>

                    <div class="landing-hero-cta">
                        <x-landing.boton-play />
                    </div>

                    <ul class="landing-chips" aria-label="{{ $t['hero']['chipsAria'] }}">
                        @foreach ($t['hero']['chips'] as $chip)
                            <li>{{ $chip }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="landing-telefonos">
                <div class="landing-decoracion" aria-hidden="true"></div>
                @foreach ($t['hero']['capturas'] as $captura)
                    <div class="landing-telefono landing-telefono-{{ $loop->first ? 'uno' : 'dos' }}">
                        <x-landing.captura :archivo="$captura['archivo']" :titulo="$captura['titulo']" :detalle="$captura['detalle'] ?? null" />
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ============ PROBAR ONLINE (componente nuevo) ============ --}}

        <x-landing.probar-online />

        {{-- ============ NÚMEROS ============ --}}

        <section class="landing-franja" aria-label="{{ $t['numeros']['aria'] }}">
            <div class="landing-franja-interior">
                <ul class="landing-numeros">
                    @foreach ($t['numeros']['items'] as $numero)
                        <li><strong>{{ $numero['n'] }}</strong><span>{{ $numero['t'] }}</span></li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- ============ EL PROBLEMA ============ --}}

        <section class="landing-siempre" aria-labelledby="landing-problema-titulo">
            <div class="landing-siempre-interior">
                <x-landing.cabecera :datos="$t['problema']" id="landing-problema-titulo" clase="landing-siempre-cabecera" />
                <x-landing.columnas :items="$t['problema']['columnas']" />
            </div>
        </section>

        {{-- ============ CÓMO FUNCIONA ============ --}}

        <section class="landing-experiencia" id="funciones" aria-labelledby="landing-pasos-titulo">
            <div class="landing-experiencia-interior">
                <x-landing.cabecera :datos="$t['pasos']" id="landing-pasos-titulo" />
                <x-landing.pasos :items="$t['pasos']['items']" clase="landing-pasos" />
            </div>
        </section>

        {{-- ============ EXPERIENCIA MEJORADA ============ --}}

        <section class="landing-experiencia" aria-labelledby="landing-experiencia-titulo">
            <div class="landing-experiencia-interior">
                <x-landing.cabecera :datos="$t['experiencia']" id="landing-experiencia-titulo" />

                <div class="landing-galeria">
                    @foreach ($t['experiencia']['galeria'] as $item)
                        <figure class="landing-galeria-item">
                            <x-landing.captura :archivo="$item['archivo']" :titulo="$item['titulo']" :detalle="$item['detalle'] ?? null" />
                            <figcaption>
                                <span class="landing-galeria-numero">{{ $loop->iteration }}</span>
                                <strong>{{ $item['nombre'] }}</strong>
                                <span>{{ $item['texto'] }}</span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>

                <div class="landing-desbloqueo">
                    @foreach ($t['experiencia']['desbloqueo'] as $bloque)
                        <div>
                            <h3>{{ $bloque['t'] }}</h3>
                            <p>{{ $bloque['d'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ============ FORMULARIOS ============ --}}

        <x-landing.seccion-telefonos :datos="$t['formularios']" id="landing-formularios-titulo" />

        {{-- ============ LO QUE VES ES LO QUE SALE ============ --}}

        <x-landing.seccion-panel :datos="$t['hojas']" id="landing-hojas-titulo" />

        {{-- ============ PLANTILLAS ============ --}}

        <x-landing.seccion-telefonos :datos="$t['plantillas']" id="landing-plantillas-titulo" />

        {{-- ============ ANALIZADOR + IA ============ --}}

        <x-landing.seccion-panel :datos="$t['ia']" id="landing-ia-titulo" id-seccion="ia" clase="landing-ia">
            <x-slot:etiqueta>
                <span class="landing-etiqueta landing-etiqueta-dorada">
                    <span class="landing-etiqueta-icono"><x-landing.icono nombre="destellos" /></span>
                    {{ $t['ia']['etiqueta'] }}
                </span>
            </x-slot:etiqueta>

            <div class="landing-ia-tarjetas">
                @foreach ($t['ia']['tarjetas'] as $tarjeta)
                    <div @class(['landing-ia-tarjeta', 'landing-ia-tarjeta-dorada' => ! empty($tarjeta['dorada'])])>
                        <span class="landing-ia-tag">{{ $tarjeta['tag'] }}</span>
                        <h3>{{ $tarjeta['titulo'] }}</h3>
                        <x-landing.lista :items="$tarjeta['lista']" chica />
                    </div>
                @endforeach
            </div>
        </x-landing.seccion-panel>

        {{-- ============ PERFILES Y MIS CVs ============ --}}

        <x-landing.seccion-telefonos :datos="$t['perfiles']" id="landing-perfiles-titulo" />

        {{-- ============ TODO LO QUE ES GRATIS ============ --}}

        <section class="landing-siempre" id="gratis" aria-labelledby="landing-gratis-titulo">
            <div class="landing-siempre-interior">
                <x-landing.cabecera :datos="$t['gratis']" id="landing-gratis-titulo" clase="landing-siempre-cabecera" />
                <ul class="landing-gratis">
                    @foreach ($t['gratis']['items'] as $item)
                        <li>
                            <span class="landing-lista-check" aria-hidden="true">✓</span>
                            <div>
                                <h3>{{ $item['t'] }}</h3>
                                <p>{{ $item['d'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- ============ PLANES ============ --}}

        <section class="landing-experiencia landing-planes-seccion" id="planes" aria-labelledby="landing-planes-titulo">
            <div class="landing-experiencia-interior">
                <x-landing.cabecera :datos="$t['planes']" id="landing-planes-titulo" />

                <div class="landing-planes">
                    @foreach ($t['planes']['items'] as $plan)
                        <article
                            @class(['landing-plan', 'landing-plan-destacado' => ! empty($plan['destacado']), 'landing-plan-futuro' => ! empty($plan['futuro'])])
                            aria-labelledby="plan-{{ $plan['id'] }}"
                        >
                            @if (! empty($plan['destacado']))
                                <span class="landing-plan-cinta">{{ $t['planes']['recomendado'] }}</span>
                            @endif
                            <h3 id="plan-{{ $plan['id'] }}">
                                @if (! empty($plan['corona']))
                                    <span class="landing-plan-corona"><x-landing.icono nombre="corona" /></span>
                                @endif
                                {{ $plan['nombre'] }}
                            </h3>
                            <p class="landing-plan-precio">
                                <strong>{{ $plan['precio'] }}</strong>
                                @if (! empty($plan['periodo']))
                                    <span>{{ $plan['periodo'] }}</span>
                                @endif
                            </p>
                            <p class="landing-plan-resumen">{{ $plan['resumen'] }}</p>
                            <x-landing.lista :items="$plan['incluye']" chica />
                        </article>
                    @endforeach
                </div>

                <ul class="landing-garantias" aria-label="{{ $t['planes']['garantiasAria'] }}">
                    @foreach ($t['planes']['garantias'] as $garantia)
                        <li>{{ $garantia }}</li>
                    @endforeach
                </ul>
                <p class="landing-planes-nota">{{ $t['planes']['nota'] }}</p>
            </div>
        </section>

        {{-- ============ PRIVACIDAD ============ --}}

        <section class="landing-siempre" aria-labelledby="landing-privacidad-titulo">
            <div class="landing-siempre-interior">
                <x-landing.cabecera :datos="$t['privacidad']" id="landing-privacidad-titulo" clase="landing-siempre-cabecera" />
                <x-landing.columnas :items="$t['privacidad']['columnas']" />
            </div>
        </section>

        {{-- ============ COMUNIDAD Y MODO OSCURO ============ --}}

        <x-landing.seccion-panel :datos="$t['comunidad']" id="landing-oscuro-titulo" />

        {{-- ============ ACCESIBILIDAD ============ --}}

        <section class="landing-accesibilidad" aria-labelledby="landing-accesibilidad-titulo">
            <div class="landing-accesibilidad-icono" aria-hidden="true">
                <x-landing.icono nombre="accesibilidad" />
            </div>

            <div class="landing-accesibilidad-texto">
                <span class="landing-etiqueta">{{ $t['accesibilidad']['etiqueta'] }}</span>
                <h2 id="landing-accesibilidad-titulo">{{ $t['accesibilidad']['titulo'] }}</h2>
                @foreach ($t['accesibilidad']['parrafos'] as $parrafo)
                    <p>{{ $parrafo }}</p>
                @endforeach
                <x-landing.lista :items="$t['accesibilidad']['lista']" />
            </div>

            <div class="landing-accesibilidad-capturas">
                @foreach ($t['accesibilidad']['capturas'] as $captura)
                    <figure class="landing-oscuro-item">
                        <x-landing.captura :archivo="$captura['archivo']" :titulo="$captura['titulo']" :detalle="$captura['detalle'] ?? null" />
                        <figcaption>{{ $captura['pie'] ?? '' }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </section>

        {{-- ============ DESCARGA ============ --}}

        <section class="landing-siempre" id="descargar" aria-labelledby="landing-instalar-titulo">
            <div class="landing-siempre-interior">
                <x-landing.cabecera :datos="$t['descarga']" id="landing-instalar-titulo" clase="landing-siempre-cabecera" />

                <div class="landing-descargas">
                    <x-landing.boton-play />
                    <x-landing.boton
                        :url="$urlApk"
                        :texto="$t['botones']['apk']"
                        :detalle="$t['botones']['apkDetalle']"
                        class="landing-boton-secundario"
                    >
                        <x-slot:icono><x-landing.icono nombre="descarga" /></x-slot:icono>
                    </x-landing.boton>
                </div>

                <h3 class="landing-subtitulo">{{ $t['descarga']['subtitulo'] }}</h3>
                <x-landing.pasos :items="$t['descarga']['pasos']" />
            </div>
        </section>

        {{-- ============ PREGUNTAS FRECUENTES ============ --}}

        <section class="landing-experiencia" id="preguntas" aria-labelledby="landing-faq-titulo">
            <div class="landing-experiencia-interior">
                <x-landing.cabecera :datos="$t['faq']" id="landing-faq-titulo" />
                <div class="landing-faq">
                    {{-- <details> abre y cierra solo, sin JavaScript --}}
                    @foreach ($t['faq']['items'] as $pregunta)
                        <details>
                            <summary>{{ $pregunta['p'] }}</summary>
                            <p>{{ $pregunta['r'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ============ CIERRE ============ --}}

        <section class="landing-cierre">
            <h2>{{ $t['cierre']['titulo'] }}</h2>
            <p>{{ $t['cierre']['texto'] }}</p>
            <x-landing.boton-play claro :texto="$t['cierre']['boton']" />
            <span class="landing-gratuita">{{ $t['cierre']['pie'] }}</span>
        </section>

        <x-landing.volver-arriba />

    </div>
</x-layouts::app>
