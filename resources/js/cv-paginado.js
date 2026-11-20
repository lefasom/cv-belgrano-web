/*
    VISTA PREVIA DEL CV (/crear): paginación, edición, zoom y tamaño de letra.

    PHP (Blade) arma el CV dentro de [data-cv-fuente], que está oculto. Este script:

    1. PAGINA: copia el contenido a hojas A4. Cada plantilla trae el molde de una hoja
       ([data-cv-hoja]) y su contenido por columna ([data-cv-columna]). Cada columna
       se reparte por separado (la ATS tiene una; la Personalizada, dos):
         - Un bloque suelto (encabezado, foto) se mueve entero.
         - Una sección [data-cv-seccion] se reparte por ítems. El título nunca queda
           solo al pie: viaja con su primer ítem. Si sigue en otra hoja, no se repite.
       Es la idea del paginationService de App-de-cv, simplificada.

    2. EDITA: los textos con [data-cv-campo] son contenteditable (como useCVEditor.ts
       de la app). Al salir de uno, se manda a PHP: el método editar() de la vista previa.

    3. ZOOM Y DESPLAZAR: se elige la herramienta (lupa o mano) y se usa con Ctrl + clic
       arrastrando; Ctrl + rueda siempre hace zoom. En el celular: pellizcar y dos dedos.

    4. LETRA: A− / A+ cambian --tamano-letra y se vuelve a paginar.
*/

const LETRA = { min: 0.85, max: 1.15, paso: 0.05 };
const ZOOM = { min: 1, max: 3 };

const vista = document.querySelector('[data-cv-vista]');

if (vista) {
    const $ = (selector) => vista.querySelector(selector);
    const fuente = $('[data-cv-fuente]');
    const ventana = $('[data-cv-ventana]');
    const marco = $('[data-cv-marco]');
    const paginas = $('[data-cv-paginas]');

    let zoom = 1;
    let escala = 1;

    // =====================================================================
    // 1. PAGINACIÓN
    // =====================================================================

    /** Hasta dónde puede llegar el contenido en un lugar de la hoja (sin su padding de abajo). */
    function limiteDe(lugar) {
        return lugar.offsetTop + lugar.clientHeight - parseFloat(getComputedStyle(lugar).paddingBottom);
    }

    /** ¿El elemento termina antes del límite? offsetTop se mide desde la hoja y no lo afecta el zoom. */
    function entra(elemento, lugar) {
        return elemento.offsetTop + elemento.offsetHeight <= limiteDe(lugar);
    }

    function paginar() {
        const molde = fuente.querySelector('[data-cv-hoja]')?.firstElementChild;
        paginas.replaceChildren();

        if (! molde) return;

        // Las hojas se crean a medida que alguna columna las necesita.
        const hojas = [];
        const hoja = (numero) => {
            while (hojas.length <= numero) {
                hojas.push(paginas.appendChild(molde.cloneNode(true)));
            }
            return hojas[numero];
        };
        hoja(0);

        for (const columna of fuente.querySelectorAll('[data-cv-columna]')) {
            const nombre = columna.dataset.cvColumna;
            let numero = 0;
            let lugar = hoja(0).querySelector(`[data-cv-slot="${nombre}"]`);

            const pasarDeHoja = () => {
                numero++;
                lugar = hoja(numero).querySelector(`[data-cv-slot="${nombre}"]`);
            };

            for (const bloque of columna.children) {
                // ---- Bloque suelto: entra entero o pasa entero a la hoja siguiente ----
                if (! bloque.matches('[data-cv-seccion]')) {
                    const copia = bloque.cloneNode(true);
                    lugar.append(copia);

                    if (! entra(copia, lugar) && lugar.children.length > 1) {
                        pasarDeHoja();
                        lugar.append(copia);
                    }
                    continue;
                }

                // ---- Sección: título + ítems, de a uno ----
                const [titulo, ...items] = bloque.children;
                let seccion = bloque.cloneNode(false);
                lugar.append(seccion);

                items.forEach((item, i) => {
                    // El título va pegado al primer ítem.
                    const grupo = i === 0
                        ? [titulo.cloneNode(true), item.cloneNode(true)]
                        : [item.cloneNode(true)];

                    seccion.append(...grupo);

                    const ultimo = grupo[grupo.length - 1];
                    const hayOtraCosa = lugar.children.length > 1 || seccion.children.length > grupo.length;

                    // Si no entra (y no es lo único de la hoja, para no saltar para siempre)...
                    if (! entra(ultimo, lugar) && hayOtraCosa) {
                        grupo.forEach((nodo) => nodo.remove());
                        if (seccion.children.length === 0) seccion.remove();

                        // ...sigue en la hoja siguiente, sin repetir el título.
                        pasarDeHoja();
                        seccion = bloque.cloneNode(false);
                        lugar.append(seccion);
                        seccion.append(...grupo);
                    }
                });
            }
        }

        escalar();
    }

    // Varias causas juntas (Livewire, letras que cargan) => una sola paginación.
    let pendiente = false;
    function programarPaginado() {
        if (pendiente) return;
        pendiente = true;
        requestAnimationFrame(() => {
            pendiente = false;
            // No se pagina mientras se escribe en el CV: se perdería el cursor.
            if (! paginas.contains(document.activeElement)) paginar();
        });
    }

    // =====================================================================
    // ESCALA + ZOOM
    // =====================================================================

    /** Escala = lo que hace falta para que la hoja entre a lo ancho × el zoom. */
    function escalar() {
        const estilo = getComputedStyle(ventana);
        const disponible = ventana.clientWidth - parseFloat(estilo.paddingLeft) - parseFloat(estilo.paddingRight);
        const ajuste = Math.min(1, disponible / paginas.offsetWidth);

        escala = ajuste * zoom;
        paginas.style.transform = `scale(${escala})`;

        // transform no cambia el lugar que ocupa: el tamaño real se lo damos al marco.
        // Con zoom el marco queda más ancho que la ventana y aparece el scroll para moverse.
        marco.style.width = `${paginas.offsetWidth * escala}px`;
        marco.style.height = `${paginas.offsetHeight * escala}px`;

        const indicador = document.querySelector('[data-cv-zoom-valor]');
        if (indicador) indicador.textContent = `${Math.round(zoom * 100)}%`;
    }

    /** Punto del CV (en px sin escala) que está en la posición (px, py) de la ventana. */
    function puntoDelCv(px, py) {
        return {
            x: (ventana.scrollLeft + px - marco.offsetLeft) / escala,
            y: (ventana.scrollTop + py - marco.offsetTop) / escala,
        };
    }

    /** Mueve el scroll para que ese punto del CV quede en (px, py) de la ventana. */
    function llevarPunto(punto, px, py) {
        ventana.scrollLeft = punto.x * escala + marco.offsetLeft - px;
        ventana.scrollTop = punto.y * escala + marco.offsetTop - py;
    }

    /** (x, y) de la pantalla => (px, py) dentro de la ventana. Sin punto: el centro. */
    function enLaVentana(x, y) {
        const caja = ventana.getBoundingClientRect();
        return [
            (x ?? caja.left + ventana.clientWidth / 2) - caja.left,
            (y ?? caja.top + ventana.clientHeight / 2) - caja.top,
        ];
    }

    /**
     * Cambia el zoom dejando quieto el punto (x, y) de la pantalla:
     * lo que estaba bajo el mouse (o los dedos) sigue ahí después del zoom.
     */
    function ponerZoom(nuevo, x, y) {
        nuevo = Math.min(ZOOM.max, Math.max(ZOOM.min, nuevo));
        if (nuevo === zoom) return;

        const [px, py] = enLaVentana(x, y);
        const punto = puntoDelCv(px, py);

        zoom = nuevo;
        escalar();
        llevarPunto(punto, px, py);
    }

    // ---- Herramienta elegida: "zoom" (lupa) o "desplazar" (mano) ----
    let modo = 'zoom';

    function elegirModo(nuevo) {
        modo = nuevo;
        ventana.classList.toggle('modo-zoom', modo === 'zoom');
        ventana.classList.toggle('modo-desplazar', modo === 'desplazar');

        document.querySelectorAll('[data-cv-modo]').forEach((boton) => {
            const activo = boton.dataset.cvModo === modo;
            boton.classList.toggle('activo', activo);
            boton.setAttribute('aria-pressed', activo ? 'true' : 'false');
        });
    }

    document.addEventListener('click', (evento) => {
        const boton = evento.target.closest('[data-cv-modo]');
        if (boton) elegirModo(boton.dataset.cvModo);
    });

    elegirModo('zoom');

    // ---- Con Ctrl apretado el cursor muestra la lupa o la mano (ver crear.css) ----
    const marcarCtrl = (evento) => ventana.classList.toggle('con-ctrl', evento.ctrlKey);
    window.addEventListener('keydown', marcarCtrl);
    window.addEventListener('keyup', marcarCtrl);
    ventana.addEventListener('mousemove', marcarCtrl);
    window.addEventListener('blur', () => ventana.classList.remove('con-ctrl'));

    // ---- Ctrl + rueda: zoom (con cualquier herramienta) ----
    ventana.addEventListener('wheel', (evento) => {
        if (! evento.ctrlKey) return;
        evento.preventDefault(); // si no, el navegador hace zoom de toda la página
        ponerZoom(zoom * Math.exp(-evento.deltaY * 0.002), evento.clientX, evento.clientY);
    }, { passive: false });

    // ---- Ctrl + clic arrastrando: hace lo que diga la herramienta ----
    //   Zoom:      a la derecha o arriba acerca; a la izquierda o abajo aleja.
    //   Desplazar: el CV sigue a la mano.
    let arrastre = null;

    ventana.addEventListener('mousedown', (evento) => {
        if (! evento.ctrlKey || evento.button !== 0) return;
        evento.preventDefault(); // que no seleccione texto ni entre a editar
        arrastre = {
            x: evento.clientX,
            y: evento.clientY,
            zoom,
            scrollLeft: ventana.scrollLeft,
            scrollTop: ventana.scrollTop,
        };
        ventana.classList.add('arrastrando');
    });

    window.addEventListener('mousemove', (evento) => {
        if (! arrastre) return;
        const dx = evento.clientX - arrastre.x;
        const dy = evento.clientY - arrastre.y;

        if (modo === 'zoom') {
            ponerZoom(arrastre.zoom * Math.exp((dx - dy) / 250), arrastre.x, arrastre.y);
        } else {
            ventana.scrollLeft = arrastre.scrollLeft - dx;
            ventana.scrollTop = arrastre.scrollTop - dy;
        }
    });

    window.addEventListener('mouseup', () => {
        arrastre = null;
        ventana.classList.remove('arrastrando');
    });

    // ---- Dedos (celular) ----
    //   Un dedo: desplaza (lo hace el navegador solo, es el scroll de la ventana).
    //   Dos dedos: pellizcar = zoom, y moverlos juntos = desplazar. Las dos cosas a la vez:
    //   el punto del CV que quedó entre los dedos al apoyarlos los sigue.
    let dedos = null;
    const distancia = (t) => Math.hypot(t[0].clientX - t[1].clientX, t[0].clientY - t[1].clientY);
    const medio = (t) => enLaVentana((t[0].clientX + t[1].clientX) / 2, (t[0].clientY + t[1].clientY) / 2);

    ventana.addEventListener('touchstart', (evento) => {
        if (evento.touches.length !== 2) return;
        const [px, py] = medio(evento.touches);
        dedos = { distancia: distancia(evento.touches), zoom, punto: puntoDelCv(px, py) };
    }, { passive: true });

    ventana.addEventListener('touchmove', (evento) => {
        if (! dedos || evento.touches.length !== 2) return;
        evento.preventDefault();

        zoom = Math.min(ZOOM.max, Math.max(ZOOM.min, dedos.zoom * distancia(evento.touches) / dedos.distancia));
        escalar();
        llevarPunto(dedos.punto, ...medio(evento.touches));
    }, { passive: false });

    ventana.addEventListener('touchend', (evento) => {
        if (evento.touches.length < 2) dedos = null;
    });

    new ResizeObserver(escalar).observe(ventana);

    // =====================================================================
    // 2. EDICIÓN DESDE EL CV
    // =====================================================================

    /** El componente Livewire de la vista previa (para llamar a editar()). */
    const componente = () => window.Livewire?.find(vista.getAttribute('wire:id'));

    paginas.addEventListener('focusin', (evento) => {
        const campo = evento.target.closest('[data-cv-campo]');
        if (campo) campo.dataset.antes = campo.innerText;
    });

    paginas.addEventListener('focusout', async (evento) => {
        const campo = evento.target.closest('[data-cv-campo]');
        if (! campo) return;

        // Sin cambios: solo se pagina lo que haya quedado pendiente mientras se editaba.
        if (campo.innerText === campo.dataset.antes) {
            programarPaginado();
            return;
        }

        // PHP valida y guarda. Si el texto no vale (ej.: nombre vacío), no cambia nada
        // y al volver a paginar aparece de nuevo el texto de antes.
        await componente()?.call('editar', campo.dataset.cvCampo, campo.innerText);
        programarPaginado();
    });

    paginas.addEventListener('keydown', (evento) => {
        const campo = evento.target.closest('[data-cv-campo]');
        if (! campo) return;

        // Enter termina la edición (cada texto es de un solo renglón). Escape la cancela.
        if (evento.key === 'Enter') {
            evento.preventDefault();
            campo.blur();
        }
        if (evento.key === 'Escape') {
            campo.innerText = campo.dataset.antes;
            campo.blur();
        }
    });

    // Al pegar, solo texto (sin negritas ni colores de otra página).
    paginas.addEventListener('paste', (evento) => {
        if (! evento.target.closest('[data-cv-campo]')) return;
        evento.preventDefault();
        document.execCommand('insertText', false, evento.clipboardData.getData('text/plain'));
    });

    // =====================================================================
    // 3. BOTONES: letra, zoom y descargar (algunos están fuera de la vista previa)
    // =====================================================================

    function cambiarLetra(paso) {
        const actual = parseFloat(paginas.style.getPropertyValue('--tamano-letra')) || 1;
        const nueva = Math.min(LETRA.max, Math.max(LETRA.min, actual + paso));
        paginas.style.setProperty('--tamano-letra', nueva.toFixed(2));
        paginar();
    }

    /** Descargar: el navegador imprime solo las hojas (ver @media print en crear.css). */
    function descargar() {
        const tituloOriginal = document.title;
        const nombre = paginas.querySelector('[data-cv-campo="datos.full_name"]')?.textContent.trim();

        // El título de la pestaña es el nombre que propone el navegador para el PDF.
        if (nombre) document.title = `CV - ${nombre}`;

        window.addEventListener('afterprint', () => (document.title = tituloOriginal), { once: true });
        window.print();
    }

    const acciones = {
        'letra-menos': () => cambiarLetra(-LETRA.paso),
        'letra-mas': () => cambiarLetra(LETRA.paso),
        'zoom-normal': () => ponerZoom(1),
        descargar,
    };

    document.addEventListener('click', (evento) => {
        const boton = evento.target.closest('[data-cv-accion]');
        acciones[boton?.dataset.cvAccion]?.();
    });

    // =====================================================================
    // CUÁNDO SE VUELVE A PAGINAR
    // =====================================================================

    // Livewire cambia el contenido de la fuente (guardar un modal, editar, foto, plantilla).
    new MutationObserver(programarPaginado).observe(fuente, {
        childList: true,
        subtree: true,
        characterData: true,
        attributes: true,
    });

    // Si alguna letra termina de cargar después de paginar, las medidas cambian.
    document.fonts?.addEventListener('loadingdone', programarPaginado);

    paginar();
}
