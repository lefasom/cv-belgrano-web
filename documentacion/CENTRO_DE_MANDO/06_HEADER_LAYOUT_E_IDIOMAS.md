# 06_HEADER_LAYOUT_E_IDIOMAS

**Última actualización:** 21/11/2026

> El molde de todas las páginas, la barra superior que comparten la landing y la app, y dónde están los textos traducidos.

---

## 1. El molde: `resources/views/layouts/app.blade.php`

Se usa con `<x-layouts::app>`. Recibe:

| Prop / slot | Para qué |
| ----------- | -------- |
| `$slot` | El contenido de la página (como `children` en React) |
| `$head` | Algo extra para el `<head>`, por ejemplo el CSS de la página |
| `$title` | El título de la pestaña |
| `$menu` | Qué barra mostrar: `true` (por defecto) la de la app; `false` la de la landing |

`@props([...])` declara qué recibe, con su valor por defecto. Va **primero** en el archivo.

---

## 2. La barra superior

Antes del 20/11 la landing tenía su barra dentro de `home.blade.php` y el molde tenía otra (`.menu`). Ahora hay **una sola**, en el molde, con estas partes:

```
<header class="landing-barra [landing-barra-app]">
   marca (logo + nombre → home)
   <nav>  solo en la landing: links a sus secciones
   .landing-barra-acciones:
       landing → idioma · Crear CV online · Descargar
       app     → (sin sesión) Iniciar sesión · Crear cuenta
                 (con sesión) Hola, Nombre · Cerrar sesión
</header>
```

**Por qué el `<nav>` solo lleva links:** `<nav>` es para ir a otra página o sección; los lectores de pantalla lo anuncian como "navegación". El selector de idioma, el saludo y "Cerrar sesión" (un formulario POST) no son navegación: van en las acciones.

En la app el `<nav>` no está: "Crear CV" se sacó el 20/11 porque el logo ya lleva al inicio.

**CSS:** `resources/css/header.css` (se movió desde `home.css`; también el del selector de idioma). La clase `landing-barra-app` cambia lo que hace falta en la app: en el celular esconde el saludo y deja que las acciones bajen de línea.

---

## 3. Idiomas

El idioma lo elige el middleware `ElegirIdioma` (`bootstrap/app.php`) en **todas** las páginas: primero el elegido con el selector (sesión), si no el del navegador.

| Archivo | Textos de |
| ------- | --------- |
| `lang/*/landing.php` | La landing y su barra |
| `lang/*/layout.php` | La barra de la app: `menu.crear` (título de `/crear`), `hola` (con `:nombre`), `entrar`, `registrarse`, `salir` |
| `lang/*/cv.php` | Títulos del CV (`secciones`), estado de estudios (`estado`) y la página `/crear` (`creador`) |

### Convención del proyecto: los textos se usan como array (decisión de Leo, 21/11/2026)

Cada vista carga **el archivo entero** al principio y después lee las claves como array:

```blade
@php($t = __('cv'))          {{-- textos principales de la vista --}}
@php($l = __('layout'))      {{-- si hace falta un segundo archivo --}}

<h2>{{ $t['creador']['herramientas'] }}</h2>
<x-layouts::app :title="$l['menu']['crear']">
```

- `$t` = el archivo principal de la vista; `$l` = `layout.php` cuando se necesita también.
- **Cada componente define su propio `$t`.** Un `<x-...>` o un componente Livewire no ve las variables de la vista que lo usa.
- **Si el archivo ya tiene un bloque `@php ... @endphp`, el `$t` va adentro del bloque** (`$t = __('cv');`). Si se pone como `@php(...)` aparte, Blade junta los dos y `$t` queda sin definir (pasó en `components/cv/ats` y `ejecutivo`).
- En el código PHP de un componente Livewire (la clase): `__('cv')['creador']['fotoPesada']`.
- **Comodines como `:nombre`:** con el array, Laravel no los reemplaza. Se hace a mano: `str_replace(':nombre', $usuario, $l['menu']['hola'])`.
- **Excepción:** `App\Cv\DatosPlantilla::estado()` sigue con `__("cv.estado.$status")`, porque la clave cambia según el dato y ahí conviene que una clave que falta no rompa nada.

**⚠️ Por qué normalmente se recomienda `__('archivo.clave')` y no el array:**

| | `__('cv.creador.foto')` | `$t['creador']['foto']` |
| --- | --- | --- |
| Falta la clave en `pt` | Usa el idioma de respaldo o muestra la clave. **La página carga** | Error "Undefined array key". **La página da error 500** |
| Comodín `:nombre` | Lo reemplaza Laravel | Hay que hacerlo a mano |
| Dentro de un componente | Anda siempre | Hay que definir `$t` en cada uno |
| Buscar dónde se usa una clave | Se busca `cv.creador.foto` | Más difícil: `$t` no dice de qué archivo es |

**Por qué igual se usa el array:** se lee más corto y prolijo. **Si se trabaja bien no da ningún problema.** Trabajar bien significa:

1. **Toda clave nueva se agrega en los tres idiomas** (`es`, `en` y `pt`) a la vez.
2. Cada vista y cada componente define su `$t`.
3. Los comodines se reemplazan con `str_replace`.
4. Después de tocar textos, correr el verificador antes del commit y abrir la página en los tres idiomas:

   ```
   php herramientas/verificar_traducciones.php
   ```

   Revisa que `lang/es`, `lang/en` y `lang/pt` tengan las mismas claves y los mismos comodines, que cada `$t[...]` usado en `resources/views` exista en los tres, y que ninguna vista use `$t` o `$l` sin definirlo. Si encuentra algo, dice qué clave falta y qué vista la usa, y termina con código 1. También se corre con `composer translations:check`, y es el **primer paso de `composer ci:check`**: si falta una clave, GitHub Actions queda en rojo.

**Pendiente:** los textos de adentro de los modales y de las páginas de login y registro siguen solo en español.

---

## 4. Historial de cambios

| Fecha | Cambio |
| ----- | ------ |
| 20/11/2026 | Creación. Barra única en el molde; `header.css`; `lang/*/layout.php`; fuera "Crear CV" de la barra de la app |
| 21/11/2026 | Todas las vistas pasan a usar los textos como array (`$t = __('archivo')`). Verificado: las 375 claves usadas existen en es, en y pt, y `/`, `/crear` y `/login` cargan en los tres idiomas |
