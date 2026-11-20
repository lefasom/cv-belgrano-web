# 00_BITACORA_MAESTRA.md

### Centro de Mando, Índice y Registro Diario — CV Belgrano Web

**Versión:** 1.0

**Última actualización:** 20/11/2026 (tarde)

> **Qué es esta carpeta:** la documentación técnica de la web, con el mismo formato que `App-de-cv/documentacion`. Acá se explica **cómo funciona** cada parte y **por qué** se decidió así. Lo que aprendo día a día está en `registro/`, el plan y las reglas en `RETOMAR.md`, y los conceptos generales en `Manual.md`. Los problemas concretos que ya pasaron van en `../AUDITORIAS/`. Para estudiar el código, empezar por `../GUIA_DE_ESTUDIO.md`.

---

## 1. Índice de documentación

| Nº | Documento | Estado | Contenido breve |
| -- | --------- | ------ | --------------- |
| 00 | `00_BITACORA_MAESTRA.md` | Creado (20/11) | Este índice, el registro diario y los pendientes |
| 01 | `01_ARQUITECTURA_DEL_CREADOR.md` | Creado (20/11) | Mapa de `/crear`: qué archivo hace qué y cómo viajan los datos de un modal al PDF |
| 02 | `02_EL_CV_EN_LA_SESION.md` | Creado (20/11) | `App\Cv\BorradorCv`: el CV guardado en la sesión, sin base de datos; edición desde el CV |
| 03 | `03_PLANTILLAS_Y_PAGINACION.md` | Creado (20/11) | Plantillas ATS y Personalizada en Blade, `DatosPlantilla` y el contrato `data-cv-*` con la paginación |
| 04 | `04_VISTA_PREVIA_EDICION_ZOOM_Y_PDF.md` | Creado (20/11) | `cv-paginado.js`: hojas A4, edición sobre el CV, zoom y desplazar, letra y descarga |
| 05 | `05_FOTO_DEL_CV.md` | Creado (20/11) | Subida con Livewire, compresión con GD y por qué la foto no se guarda |
| 06 | `06_HEADER_LAYOUT_E_IDIOMAS.md` | Creado (20/11) | El molde `layouts/app`, la barra compartida y los archivos de `lang/` |
| — | `../AUDITORIAS/00_BITACORA_AUDITORIAS.md` | Creado (20/11) | Índice de errores investigados |
| — | `../GUIA_DE_ESTUDIO.md` | Creado (20/11) | Orden recomendado para estudiar el código, con preguntas y ejercicios |

---

## 2. Estado del producto al 20/11/2026

| Parte | Estado |
| ----- | ------ |
| Home (landing en es/en/pt) | Hecho (18/11). La barra ahora vive en el molde (20/11) |
| Registro, login, logout, recuperar contraseña | Hecho (17-18/11), HTML propio sin Flux |
| `/crear`: cargar datos con modales | Hecho. Experiencia y Educación se pueden elegir para editar o eliminar; Habilidades tiene lista con ✕ (20/11) |
| `/crear`: vista previa del CV en hojas A4 | Hecho (20/11). Plantillas **ATS** y **Personalizada** (diseño del Template 3 de la app) |
| Edición sobre el CV, zoom/desplazar, tamaño de letra | Hecho (20/11) |
| Foto | Hecho (20/11). Solo en la Personalizada y **no se guarda** |
| Descargar PDF | Hecho con la impresión del navegador (20/11). Generarlo en el servidor queda para después |
| Guardar el CV en la base de datos (con login) | **Pendiente.** Hoy todo vive en la sesión |
| `CvPolicy`, JOIN, README | **Pendiente** (bloques 3, 7 y siguientes de `RETOMAR.md`) |
| Pruebas | 13 en verde. **No hay pruebas** del creador nuevo todavía |

---

## 3. Registro diario

| Fecha | Commit(s) | Qué se hizo |
| ----- | --------- | ----------- |
| 15/11 | `cd326e0` … `97fb203` | Proyecto Laravel 13 + Livewire + MySQL. Tabla `cvs`. `Manual.md` y `RETOMAR.md` |
| 16/11 | `e60f3dc` … `48aa494` | Relaciones `User` ↔ `Cv`, `$fillable`, chuleta de Tinker. Plan por bloques |
| 17/11 | `a321ddb` … `cb84aff` | Tablas hijas (`experiences`, `education`, `skills`), factories y seeder. Ver `registro/2026-11-17.md` |
| 18/11 | `2530fdf` … `5bd41b7` | Sin Flux ni Tailwind. Home = landing en 3 idiomas. `/crear` con 4 modales Livewire y `#[Session]`. Ver `registro/2026-11-18.md` |
| 20/11 | `e76e048` y los dos siguientes | Ver el detalle abajo. Después: títulos estándar en la ATS y esta documentación |
| 20/11 (tarde) | Landing: `3d123c5` … `c3e2c3a` | El creador pasado a JavaScript en la landing de lanzamiento (otro repo). Ver abajo |

### 20/11/2026 — Vista previa, plantillas y herramientas

> El código de este día está comentado en español; el orden para estudiarlo está en `../GUIA_DE_ESTUDIO.md`.

**Header compartido.** La barra superior pasó de `home.blade.php` al molde `layouts/app.blade.php`. Con `:menu="false"` muestra la barra de la landing (secciones, idioma, descargar) y por defecto la de la app (iniciar sesión / crear cuenta, o saludo y cerrar sesión). Estilos en `resources/css/header.css`; textos en `lang/*/layout.php`. Se sacó el link "Crear CV" de la barra de la app. → `06`

**Nueva `/crear`.** A la izquierda, los cuatro botones de los modales y el panel **Herramientas** (foto, tamaño de letra, zoom y desplazar). A la derecha, pestañas **Plantilla ATS / Personalizada** y la vista previa (90vh con scroll) con **Descargar PDF** abajo. En el celular, el panel arriba y la vista previa abajo. → `01`

**El CV en la sesión.** Los componentes dejaron de usar `#[Session]`, cada uno con su copia: ahora todo pasa por `App\Cv\BorradorCv`. Motivo: con la edición sobre el CV, la copia vieja de un modal pisaba lo editado. → `02`

**Plantillas en Blade.** `components/cv/ats.blade.php` (de `TemplateATS.ts`) y `components/cv/ejecutivo.blade.php` (de `Template3.ts`, el 3.png). `App\Cv\DatosPlantilla` filtra lo vacío, como el filtrado de `CVData` de la app. Títulos en `lang/*/cv.php` según el idioma del sitio. **La ATS tiene sus propios títulos** (`cv.ats`, de `traductorTemplateATS`): "Resumen Profesional · Experiencia · Educación · Habilidades", sin "&", para que un ATS no se confunda. → `03`

**Vista previa (JavaScript).** `resources/js/cv-paginado.js` reparte el CV en hojas A4 por columna, sin dejar un título solo al pie. Edición con `contenteditable` (como `useCVEditor.ts`), zoom y desplazar con Ctrl + arrastrar, Ctrl + rueda y dos dedos, y letra A−/A+. El PDF se descarga con `window.print()`. → `04`

**Foto.** Subida con `WithFileUploads` y achicada con GD a 500×500 en JPEG (como `AvatarPicker.tsx`). No se guarda en ningún lado. → `05`

**Error 422 al subir la foto.** `php artisan serve` en Windows no le pasa `TEMP`/`TMP` al servidor de PHP. Se agregaron en `AppServiceProvider`. → `../AUDITORIAS/01`

**Modales.** Experiencia y Educación muestran "Experiencia 1, 2, 3… + Nueva" para elegir cuál editar o eliminar (`components/creador/selector.blade.php`). Habilidades muestra la lista completa con ✕ para eliminar y un campo para agregar sin cerrar el modal.

**Cómo se probó.** Chrome sin ventana controlado por un script (carga con los modales, edición, zoom, foto, cambio de plantilla, recarga y PDF), más `php artisan test` (13 en verde). El pellizco con dos dedos **no se probó en un celular real**.

**Decisiones del día:**

| Decisión | Por qué |
| -------- | ------- |
| Plantillas en Blade (PHP) y no copiar el JavaScript de la app | Es lo que muestra Laravel en el portfolio; `@if` reemplaza los `${cond ? … : ""}` |
| Paginación propia y chica, no el `paginationService.ts` de la app | El de la app (3126 líneas) resuelve cosas del WebView y de plantillas complejas; para estas dos plantillas alcanza una versión corta |
| PDF con la impresión del navegador | Gratis, sale igual a la vista previa y con texto seleccionable (lo necesita un ATS). Chrome en el servidor necesita un VPS |
| El idioma del CV es el del sitio | Lo decidió Leo: no hay selector aparte |
| La foto no se guarda | Lo pidió Leo: nada de base de datos todavía, y la foto tampoco en la sesión |
| Títulos propios para la ATS | Lo pidió Leo: en un CV ATS los títulos tienen que ser estándar ("Educación", no "Educación & cursos") |

### 20/11/2026 (tarde) — El creador en la landing de lanzamiento (otro repo)

> Repo `landingpage-cvbelgrano` (`C:\Users\leo\Desktop\landing-cv-belgrano`, Vite + React). Commits `3d123c5`, `1edf78b` y `c3e2c3a`, ya subidos. El detalle está en el README de ese repo.

**Qué se hizo.** El `/crear` de esta web se pasó a JavaScript para la landing de lanzamiento: **sin PHP, sin base de datos y sin login**. Es una segunda página (`crear/index.html`) con los cuatro modales, las plantillas ATS y Personalizada en React, la vista previa A4 (`vistaPrevia.js`, adaptado de `cv-paginado.js`), la edición sobre el CV, la letra, el zoom, la foto (achicada con `<canvas>` en vez de GD) y el PDF por impresión. La landing suma el botón "Crear CV online", la sección "Probá crear tu CV ahora" y el botón volver arriba, igual que aquí.

**Equivalencias con esta web:**

| Laravel (esta web) | Landing (JavaScript) |
| ------------------ | -------------------- |
| `App\Cv\BorradorCv` + la sesión | `borradorCv.js` + `localStorage` (`cv_borrador`) |
| `App\Cv\DatosPlantilla` | `datosPlantilla.js` |
| `App\Cv\FotoCv` (GD) | `fotoCv.js` (`<canvas>`) |
| Componentes Livewire de los modales | `ModalDatos.jsx`, `ModalLista.jsx` (Experiencia y Educación), `ModalHabilidades.jsx` |
| `components/cv/*.blade.php` | `plantillas/Ats.jsx` y `plantillas/Ejecutivo.jsx` |
| `lang/*/cv.php` | `src/crear/textos.js` (con los modales también traducidos) |

**Agregado allá:** el botón "Empezar de cero", porque el `localStorage` no vence como la sesión.

**Cómo se probó.** Con Chrome sin ventana, controlado por un script: 32 controles en verde (validaciones, cargar, editar y eliminar, edición sobre el CV, letra, zoom, foto, plantilla, idioma, PDF con una página por hoja, recarga y celular), sin errores en la consola. El pellizco **no se probó en un celular real**.

**GitHub Actions de esta web:** se explicaron Pint, PHPStan y los genéricos de las relaciones, y **quedó arreglado**: Pint formateó 14 archivos; PHPStan (nivel 7) pasó de 32 errores a 0 con tipos de arrays en `app/Cv` (alias `@phpstan-type`), controles de `false` en GD y `preg_split`, genéricos en las 8 relaciones y `Cv $cv` en el seeder. Sin `@phpstan-ignore` ni baseline.

---

## 4. Pendientes

| Tema | Notas |
| ---- | ----- |
| Guardar el CV en la base de datos | Con login: pasar lo de `BorradorCv` a `cvs` y sus tablas hijas, con `CvPolicy` |
| Pruebas del creador | `BorradorCv::editar()` es un buen primer candidato (es PHP puro, sin navegador) |
| Textos de los modales en `lang/` | Siguen solo en español |
| Editar el estado de un estudio desde el CV | Hoy solo desde el modal (es una opción, no texto libre) |
| PDF en el servidor | `spatie/laravel-pdf` (Chrome) cuando haya VPS |
| Probar el pellizco en un celular | Solo se probó con toques simulados |

---

## 5. Historial de cambios de este documento

| Fecha | Versión | Cambio |
| ----- | ------- | ------ |
| 20/11/2026 | 1.0 | Creación de la carpeta `documentacion` y de esta bitácora |
| 20/11/2026 | 1.1 | El creador pasado a la landing en JavaScript; GitHub Actions arreglado |
