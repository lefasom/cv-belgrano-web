# GUÍA DE ESTUDIO — Creador de CV (20/11/2026)

**Última actualización:** 20/11/2026

> Guía para estudiar el código del 20/11 (commit `e76e048` y siguientes) **hasta poder explicarlo y modificarlo solo**. La explicación de cada parte está en `CENTRO_DE_MANDO/01` a `06`; acá va **el orden y el método**.

---

## 1. Cómo estudiarlo (recomendación)

1. **Con la página abierta.** `php artisan serve` + `npm run dev` y `/crear` en el navegador. Leés un archivo y enseguida probás lo que hace.
2. **De afuera hacia adentro.** Primero qué hace la pantalla, después qué componente lo hace y al final qué clase de PHP guarda el dato. No empieces por el JavaScript.
3. **Un archivo por vez, entero.** Los comentarios del código explican el porqué; leelos, no los saltees.
4. **Romper a propósito.** La mejor forma de entender una línea es comentarla y ver qué deja de andar. Después la volvés a poner (`git restore archivo` si te perdés).
5. **Compararlo con lo que ya sabés.** Cada parte tiene su equivalente en React o en la app (tabla del punto 3).
6. **Escribirlo vos.** Cada etapa termina con un ejercicio. Lo hacés vos, sin copiar (regla 1 de `RETOMAR.md`).
7. **Explicarlo en voz alta.** Si no lo podés explicar en dos frases, todavía no está. Las preguntas de cada etapa sirven para eso, y también para la entrevista.

**Tiempo estimado:** 5 etapas de una mañana cada una (3 a 4 h). La etapa 5 es opcional antes de postular.

---

## 2. Etapas

### Etapa 1 — Dónde viven los datos (la sesión)

| Leer | Documento |
| ---- | --------- |
| `app/Cv/BorradorCv.php` | `CENTRO_DE_MANDO/02_EL_CV_EN_LA_SESION.md` |

**Conceptos:** `session()->put / get / push`, claves con punto, métodos `static`, `const`, `array_values`, `array_merge`.

**Probá en Tinker** (`php artisan tinker`):

```php
session()->put('cv.habilidades', [['name' => 'PHP']]);
App\Cv\BorradorCv::lista('habilidades');
App\Cv\BorradorCv::editar('habilidades.0.name', 'Laravel');   // true
App\Cv\BorradorCv::editar('datos.user_id', '5');              // false: ¿por qué?
App\Cv\BorradorCv::renglones("- uno\n\n• dos");                // ['uno', 'dos']
```

**Preguntas:**
1. ¿Por qué los datos siguen al recargar y la foto no?
2. ¿Qué hace la constante `EDITABLES` y por qué es importante para la seguridad?
3. ¿Por qué se dejó de usar `#[Session]` en cada componente?

**Ejercicio:** escribí una prueba en Pest (`tests/Unit/BorradorCvTest.php`) que compruebe que `editar('datos.full_name', '')` devuelve `false` y no borra el nombre.

---

### Etapa 2 — Los componentes Livewire de los modales

| Leer, en este orden | Documento |
| ------------------- | --------- |
| `livewire/creador/habilidades.blade.php` (el más simple) | `CENTRO_DE_MANDO/01_ARQUITECTURA_DEL_CREADOR.md` |
| `livewire/creador/datos.blade.php` | |
| `livewire/creador/experiencia.blade.php` | |
| `components/creador/selector.blade.php` | |
| `livewire/creador/educacion.blade.php` (compará con experiencia: es casi igual) | |

**Conceptos:** componente de archivo único (`new class extends Component`), propiedades públicas = estado, `wire:click`, `wire:submit`, `wire:model`, `validate()` con mensajes, `$this->dispatch()`, `wire:confirm`, `wire:loading`, `@props`, `@class`, `wire:key`.

**Probá:** abrí F12 → Red y mirá qué petición sale al tocar "Agregar" en habilidades. Fijate qué viaja.

**Preguntas:**
1. ¿Qué diferencia hay entre `$borrador` y lo que está en la sesión? ¿Por qué "Cancelar" no cambia nada?
2. En experiencia, ¿qué significa `$elegida === null`?
3. ¿Para qué sirve `wire:key` en la lista de habilidades?

**Ejercicio:** pasá los textos del modal de habilidades ("Nueva habilidad", "Todavía no agregaste habilidades.") a `lang/*/cv.php` (en los tres idiomas) y usalos con el `$t` del archivo: `$t['creador']['...']`.

---

### Etapa 3 — La vista previa y las plantillas (Blade)

| Leer | Documento |
| ---- | --------- |
| `livewire/creador/vista-previa.blade.php` | `CENTRO_DE_MANDO/03_PLANTILLAS_Y_PAGINACION.md` |
| `app/Cv/DatosPlantilla.php` | |
| `components/cv/ats.blade.php` | |
| `components/cv/ejecutivo.blade.php` | |
| `resources/css/cv/hojas.css`, `ats.css`, `ejecutivo.css` | |

**Conceptos:** `#[On('evento')]`, `#[Computed]` y `unset($this->cv)`, `<x-dynamic-component>`, colecciones (`collect()->map()->filter()->values()`), `@if` / `@foreach` / `$loop->first`, `{{ }}` contra `{!! !!}`, `HtmlString`, `__()` con claves anidadas.

**Probá:** en `ats.blade.php` cambiá un `{{ $cv['nombre'] }}` por `{!! $cv['nombre'] !!}` y cargá como nombre `<b>Ana</b>`. Mirá la diferencia y volvelo a `{{ }}`.

**Preguntas:**
1. ¿Cómo se entera la vista previa de que guardaste una habilidad?
2. ¿Para qué sirve el `'i'` de cada ítem en `DatosPlantilla`?
3. ¿Por qué la ATS tiene títulos distintos de la Personalizada?
4. ¿Qué pasaría si usaras `{!! !!}` con lo que escribe el usuario?

**Ejercicio:** en `DatosPlantilla`, hacé que una habilidad repetida (por ejemplo "PHP" cargada dos veces) salga una sola vez en el CV. Pista: las colecciones tienen `unique()`. Ojo: cada habilidad tiene que conservar su `'i'` original, o la edición desde el CV apuntaría a otra.

---

### Etapa 4 — La foto y el `AppServiceProvider`

| Leer | Documento |
| ---- | --------- |
| `livewire/creador/foto.blade.php` | `CENTRO_DE_MANDO/05_FOTO_DEL_CV.md` |
| `app/Cv/FotoCv.php` | `AUDITORIAS/01_ERROR_422_AL_SUBIR_LA_FOTO.md` |
| `app/Providers/AppServiceProvider.php` | |

**Conceptos:** `WithFileUploads`, hooks `updated{Propiedad}`, validación de archivos (`image`, `mimes`, `max`), GD, *data URI*, Service Providers y `boot()`, variables de entorno.

**Preguntas:**
1. ¿Dónde está la foto mientras la página está abierta? ¿Y el archivo original?
2. ¿Por qué `ponerFoto()` revisa que empiece con `data:image/jpeg;base64,`?
3. ¿Por qué fallaba la subida con `artisan serve` y por qué hubo que reiniciarlo?

**Ejercicio:** cambiá `FotoCv::LADO` a 300, subí una foto y compará cuánto pesa el texto de la imagen (F12 → Elementos → el `src` del `<img>`). Volvelo a 500.

---

### Etapa 5 — El molde, el header y el JavaScript

| Leer | Documento |
| ---- | --------- |
| `layouts/app.blade.php` y `resources/css/header.css` | `CENTRO_DE_MANDO/06_HEADER_LAYOUT_E_IDIOMAS.md` |
| `resources/views/crear.blade.php` y `resources/css/crear.css` (sobre todo `@media print`) | `CENTRO_DE_MANDO/04_VISTA_PREVIA_EDICION_ZOOM_Y_PDF.md` |
| `resources/js/cv-paginado.js` | |

El JavaScript no es Laravel: alcanza con entender **qué hace cada función** (`paginar`, `escalar`, `ponerZoom`, la edición) y **por qué esa parte no la puede hacer PHP**.

**Preguntas:**
1. ¿Para qué sirve `wire:ignore` en el marco de las hojas?
2. ¿Por qué el título de una sección viaja pegado a su primer ítem?
3. ¿Por qué el zoom y el desplazar piden Ctrl?

**Ejercicio:** cambiá el límite de letra (`LETRA.max`) a 1.3 y probá A+ hasta el final.

---

## 3. Equivalencias con lo que ya sabés

| En este proyecto | En React / la app |
| ---------------- | ----------------- |
| Propiedad pública de Livewire (`public bool $abierto`) | `useState` |
| `$this->dispatch('cv-actualizado')` + `#[On(...)]` | Levantar el estado o un *event bus* |
| `BorradorCv` (sesión) | `useCVStore` + `AsyncStorage` |
| `#[Computed]` | `useMemo` |
| Componente Blade con `@props` | Componente con props |
| `$slot` del molde | `children` |
| `DatosPlantilla::desde()` | `renderFilter` / filtrado de `CVData` |
| Plantilla Blade (`ats.blade.php`) | `getTemplateATSHtml()` |
| `cv-paginado.js` | `paginationService.ts` (versión chica) |
| `contenteditable` + `editar()` | `useCVEditor.ts` + `SAVE_ELEMENT` |
| `FotoCv::comprimir()` | `ImageManipulator.manipulateAsync` |
| `lang/*/cv.php` + `__()` | `traductorTemplate.ts` |

---

## 4. Para la entrevista

Si te preguntan por el proyecto, practicá contar estas decisiones en una o dos frases cada una:

1. **Sesión antes que base de datos:** se puede armar el CV sin cuenta; con login se guarda (pendiente). Las claves ya son los nombres de las columnas.
2. **Una sola clase para los datos** (`BorradorCv`): se evitó que una copia vieja pisara lo editado.
3. **No confiar en lo que manda el navegador:** `EDITABLES`, largos máximos y validación de la foto.
4. **Plantillas en Blade, paginación en JavaScript:** el servidor decide qué mostrar y el navegador mide dónde cortar.
5. **PDF con la impresión del navegador:** texto real para el ATS, sin depender de Chrome en el servidor.
6. **Títulos estándar en la ATS:** "Educación" y no "Educación & cursos", para que el sistema reconozca la sección.

---

## 5. Cuando termines

Marcá cada etapa en `RETOMAR.md` (sección 5, registro de avance) y anotá lo que te costó en un `registro/AAAA-MM-DD.md`, como el del 18/11.
