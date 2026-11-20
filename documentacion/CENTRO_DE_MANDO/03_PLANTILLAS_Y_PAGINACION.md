# 03_PLANTILLAS_Y_PAGINACION

**Última actualización:** 20/11/2026

> Cómo están hechas las plantillas del CV en Blade, qué reciben y qué "contrato" cumplen para que `cv-paginado.js` las reparta en hojas A4.

---

## 1. Las plantillas

| Pestaña | Componente Blade | Viene de (App-de-cv) | Diseño |
| ------- | ---------------- | -------------------- | ------ |
| Plantilla ATS | `components/cv/ats.blade.php` + `css/cv/ats.css` | `templates/TemplateATS.ts` | Una columna, Arial, `#111` y `#6b7280`, sin foto. Lo lee bien un ATS |
| Personalizada | `components/cv/ejecutivo.blade.php` + `css/cv/ejecutivo.css` | `templates/administration/Template3.ts` (3.png) | Lateral gris con títulos dorados, nombre serif en mayúsculas, foto a la derecha, bandas azul marino |

Se elige en `vista-previa` con la propiedad `$plantilla` y la constante `PLANTILLAS`:

```php
public const PLANTILLAS = ['ats' => 'cv.ats', 'personalizada' => 'cv.ejecutivo'];
```

```blade
<x-dynamic-component :component="$this::PLANTILLAS[$plantilla]" :cv="$this->cv" />
```

`<x-dynamic-component>` es `<x-cv.ats>` o `<x-cv.ejecutivo>`, según el valor.

**Solo las cuatro secciones de la web:** datos, perfil, experiencia, educación y habilidades. Proyectos, idiomas, redes, listas, etc. de la app no están, porque no existen todavía en la web.

---

## 2. Lo que reciben: `DatosPlantilla::desde()`

Es el "filtrado de CV data" de la app. Recibe lo de la sesión (más la foto) y devuelve:

```php
[
    'nombre' => 'Ana López', 'profesion' => '…', 'email' => '…',
    'telefono' => '', 'ubicacion' => '…', 'resumen' => '…',
    'experiencias' => [ ['i' => 0, 'puesto' => '…', 'empresa' => '…', 'periodo' => '…', 'tareas' => ['…', '…']] ],
    'estudios'     => [ ['i' => 0, 'titulo' => '…', 'institucion' => '…', 'periodo' => '…', 'estado' => 'Terminado'] ],
    'habilidades'  => [ ['i' => 1, 'nombre' => 'PHP'] ],
    'foto' => null,
    'vacio' => false,
]
```

- **Lo vacío no viene.** Una experiencia sin nada, o una habilidad en blanco, se filtra. Así la plantilla solo pregunta `@if ($cv['experiencias'])` y, si no hay, no aparece ni el título.
- **`'i'` es el índice original** (el `originalIndex` de la app). Si la habilidad 0 estaba en blanco y se filtró, "PHP" sigue teniendo `'i' => 1`, y el `data-cv-campo` apunta al lugar correcto de la sesión.
- **`tareas`:** cada renglón de la descripción es una viñeta (`BorradorCv::renglones`).
- **`estado`:** se traduce con `__('cv.estado.Completed')`.
- **`vacio`:** `true` si no hay nada para mostrar. Entonces la plantilla muestra "Tu CV aparece acá…", que no sale al imprimir.

---

## 3. El contrato con la paginación (`data-cv-*`)

Toda plantilla tiene que armar esto (lo cumplen las dos):

```blade
{{-- 1. El molde de UNA hoja A4, con un lugar por columna --}}
<div data-cv-hoja>
    <div class="cv-hoja cv-eje">
        <div class="cv-eje-lateral"   data-cv-slot="lateral"></div>
        <div class="cv-eje-principal" data-cv-slot="principal"></div>
    </div>
</div>

{{-- 2. El contenido de cada columna, en orden --}}
<div data-cv-columna="lateral"> … </div>
<div data-cv-columna="principal">
    <header>…</header>                              {{-- bloque suelto: se mueve entero --}}
    <section data-cv-seccion>                       {{-- sección: se reparte por ítems --}}
        <h2>Experiencia laboral</h2>                {{-- 1.er hijo = título --}}
        <div>experiencia 1</div>                    {{-- el resto = ítems --}}
        <div>experiencia 2</div>
    </section>
</div>
```

| Atributo | Significa |
| -------- | --------- |
| `data-cv-hoja` | El molde que se copia por cada hoja |
| `data-cv-slot="x"` | Dónde va la columna `x` dentro de la hoja |
| `data-cv-columna="x"` | El contenido de la columna `x` |
| `data-cv-seccion` | Se puede partir entre hojas. Su **primer hijo es el título** y el resto son ítems |
| `data-cv-campo="…"` | El texto se edita desde el CV (ver `02`) |

**Reglas de CSS que la paginación necesita:**

- `.cv-hoja` tiene `position: relative`, `height: 297mm` y `overflow: hidden`.
- **Las columnas y las secciones no tienen `position`.** La paginación mide con `offsetTop` desde la hoja; si una columna fuera `relative`, las medidas tomarían otra referencia. Los ítems sí pueden tenerla (es su propio lugar, no el de sus padres).
- El espacio entre ítems va con `.item + .item { margin-top }` y no con `gap` en el padre, porque la sección se clona vacía en cada hoja.

---

## 4. Cómo se agrega una plantilla nueva

1. Copiar `ejecutivo.blade.php` (dos columnas) o `ats.blade.php` (una) a `components/cv/nueva.blade.php`.
2. Cambiar las clases y el molde `data-cv-hoja`. Respetar el contrato del punto 3.
3. Crear `resources/css/cv/nueva.css` con las medidas usando `calc(Npx * var(--tamano-letra))`, así funcionan A− / A+.
4. Sumarlo a `vite.config.js` y al `@vite` de `vista-previa.blade.php`.
5. Agregarlo a `PLANTILLAS` y a las pestañas de `vista-previa.blade.php`, con su texto en `lang/*/cv.php`.

---

## 5. Traducciones del CV

`lang/{es,en,pt}/cv.php` tiene **dos juegos de títulos**, igual que `constants/traductores/traductorTemplate.ts` de la app:

| Clave | La usa | Títulos (es) | Viene de |
| ----- | ------ | ------------ | -------- |
| `ats` | Plantilla ATS | Resumen Profesional · Experiencia · Educación · Habilidades | `traductorTemplateATS` |
| `secciones` | Personalizada | Perfil profesional · Experiencia laboral · Educación & cursos · Habilidades & competencias | `traductorTemplate` |

**Por qué la ATS tiene los suyos:** un sistema ATS busca títulos estándar para reconocer cada sección. "Educación & cursos" o "Habilidades & competencias" pueden confundirlo; "Educación" y "Habilidades" no. En inglés: Summary · Experience · Education · Skills.

`estado` traduce el estado de los estudios (Terminado / Completed / Concluído) en las dos plantillas. **El idioma del CV es el del sitio** (decisión de Leo): no hay selector aparte.

---

## 6. Historial de cambios

| Fecha | Cambio |
| ----- | ------ |
| 20/11/2026 | Creación. La Personalizada empezó con el Template 5 ("Expediente") y ese mismo día se cambió por el Template 3 (3.png) |
| 20/11/2026 | La ATS pasa a usar sus propios títulos (`cv.ats`, de `traductorTemplateATS`), sin "&" |
