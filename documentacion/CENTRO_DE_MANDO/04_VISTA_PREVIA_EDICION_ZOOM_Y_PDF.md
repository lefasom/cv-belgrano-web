# 04_VISTA_PREVIA_EDICION_ZOOM_Y_PDF

**Última actualización:** 20/11/2026

> Qué hace `resources/js/cv-paginado.js`, la única parte del creador escrita en JavaScript, y por qué esa parte no la puede hacer PHP.

---

## 1. Por qué JavaScript

PHP arma el HTML en el servidor, pero **no sabe cuánto mide** un texto en pantalla: eso depende de la letra, del ancho y del navegador. Para saber dónde cortar una hoja A4 hay que medir en el navegador. Lo mismo con el zoom, los gestos y la impresión.

| Lo hace PHP (Blade + Livewire) | Lo hace JavaScript |
| ------------------------------ | ------------------ |
| Qué se muestra, qué se oculta, en qué orden, con qué textos | Dónde se corta cada hoja |
| Validar y guardar | Zoom, desplazar, gestos con dos dedos |
| Traducir | Tamaño de letra en vivo |
| Recibir lo editado | Detectar que se editó un texto |

---

## 2. Las piezas en el HTML

```
[data-cv-vista]            ← raíz del componente vista-previa
 ├─ pestañas de plantilla
 ├─ [data-cv-fuente] hidden ← el CV que arma PHP (oculto)
 ├─ [data-cv-ventana]       ← la ventana de 90vh con scroll
 │   └─ [data-cv-marco] wire:ignore
 │       └─ [data-cv-paginas]  ← acá el JS pone las hojas A4
 └─ Descargar PDF
```

`wire:ignore` le dice a Livewire que no toque esa parte: la maneja el JavaScript. Si no, cada vez que Livewire redibuja borraría las hojas.

---

## 3. Paginación

1. Se vacía `[data-cv-paginas]` y se crea la hoja 1 copiando el molde `[data-cv-hoja]`.
2. Para **cada columna** (`[data-cv-columna]`) por separado:
   - **Bloque suelto** (encabezado, foto): se copia a la hoja. Si se pasa del límite, va entero a la hoja siguiente.
   - **Sección** (`[data-cv-seccion]`): se copia el contenedor vacío y se agregan los ítems de a uno. **El título viaja pegado al primer ítem**, así nunca queda solo al pie. Si un ítem no entra, sigue en la hoja siguiente dentro de una copia nueva de la sección, sin repetir el título.
3. **El límite** es el alto del lugar de la columna menos su `padding` de abajo (`limiteDe`). Se mide con `offsetTop + offsetHeight`, que no cambia con el zoom.
4. Si un solo bloque es más alto que una hoja, se deja donde está (si no, saltaría de hoja para siempre).

**Cuándo se vuelve a paginar:**

| Causa | Cómo se detecta |
| ----- | --------------- |
| Livewire cambió el CV (modal, edición, foto, plantilla) | `MutationObserver` sobre `[data-cv-fuente]` |
| A− / A+ | El botón llama a `paginar()` |
| Terminó de cargar una letra | `document.fonts` → `loadingdone` |
| Se cambió el tamaño de la ventana | Solo se reescala (`ResizeObserver`), no se pagina |

No se pagina mientras se escribe en el CV (se perdería el cursor). Queda pendiente y se hace al salir del texto.

---

## 4. Escala y zoom

- **Escala** = lo que hace falta para que la hoja (210 mm) entre a lo ancho de la ventana × el **zoom** (1 a 3).
- Se aplica con `transform: scale()` sobre `[data-cv-paginas]`. Como `transform` no cambia el lugar que ocupa, el JS le da el tamaño real al marco. Con zoom, el marco queda más ancho que la ventana y aparece el scroll.
- `ponerZoom()` deja **quieto el punto bajo el mouse o los dedos**: calcula qué punto del CV está ahí (`puntoDelCv`), cambia la escala y mueve el scroll para que ese punto siga en el mismo lugar (`llevarPunto`).

| Gesto | Compu | Celular |
| ----- | ----- | ------- |
| Herramienta **Zoom** (lupa) | Ctrl + clic arrastrando: derecha o arriba acerca, izquierda o abajo aleja | Pellizcar |
| Herramienta **Desplazar** (mano) | Ctrl + clic arrastrando: el CV sigue a la mano | Arrastrar con uno o dos dedos |
| Siempre | Ctrl + rueda = zoom | Dos dedos: zoom y desplazar a la vez |
| Volver | Botón "100%" | Botón "100%" |

**¿Por qué Ctrl?** Sin Ctrl, un clic edita el texto del CV. Con Ctrl apretado, el cursor muestra la lupa o la mano (clase `con-ctrl` en la ventana).

---

## 5. Edición sobre el CV

Misma idea que `hooks/useCVEditor.ts` de la app:

1. Los textos con `data-cv-campo` son `contenteditable` (lo pone la plantilla).
2. `focusin`: se guarda el texto de antes.
3. `focusout`: si cambió, se llama a PHP con `Livewire.find(id).call('editar', campo, texto)`. Después se vuelve a paginar.
4. **Enter** termina la edición; **Escape** la cancela y vuelve al texto de antes.
5. **Pegar** inserta solo texto, sin negritas ni colores de otra página.

PHP valida en `BorradorCv::editar()` (ver `02`). Si rechaza el texto, al volver a paginar aparece el de antes.

---

## 6. Tamaño de letra

Las medidas de las plantillas son `calc(11px * var(--tamano-letra))`. A− / A+ cambian `--tamano-letra` en `[data-cv-paginas]` entre 0,85 y 1,15 (de a 0,05) y se vuelve a paginar, porque con otra letra cambia dónde se cortan las hojas. Es la misma idea que `cvSizeService` de la app. **No se guarda:** al recargar vuelve a 1.

---

## 7. Descargar PDF

`descargar()` cambia el título de la pestaña a "CV - Nombre" (es el nombre que propone el navegador para el archivo) y llama a `window.print()`. El `@media print` de `crear.css`:

- oculta el header, el panel, las pestañas y el pie;
- quita la escala y el zoom;
- deja cada hoja en una página A4 (`@page { size: A4; margin: 0 }` y `break-after: page`);
- borra el resaltado de edición.

El texto queda seleccionable en el PDF, que es lo que necesita un ATS.

**Otras formas de generar el PDF (para después):**

| Opción | A favor | En contra |
| ------ | ------- | --------- |
| Impresión del navegador (hoy) | Gratis, igual a la vista previa | El usuario pasa por el diálogo de imprimir |
| Chrome en el servidor (`spatie/laravel-pdf`) | Un clic, mismo template | Necesita VPS con Node y Chrome |
| dompdf / mPDF | Anda en cualquier hosting | CSS limitado (sin flex): habría que hacer otro CSS |
| Gotenberg o API paga | No instalar Chrome en el servidor | Otro servicio que mantener o pagar |
| html2pdf / jsPDF | Todo en el navegador | Suele generar una imagen: un ATS no la lee |

---

## 8. Historial de cambios

| Fecha | Cambio |
| ----- | ------ |
| 20/11/2026 | Creación. El zoom empezó con botones − / + y ese día se cambió por las herramientas Zoom / Desplazar |
