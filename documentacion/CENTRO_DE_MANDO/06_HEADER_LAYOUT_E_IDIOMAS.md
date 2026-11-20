# 06_HEADER_LAYOUT_E_IDIOMAS

**Última actualización:** 20/11/2026

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

**Comodín `:nombre`:** en el archivo dice `'Hola, :nombre'` y en la vista `__('layout.menu.hola', ['nombre' => $usuario])`. Así cada idioma pone el nombre donde le corresponde en la frase.

**Pendiente:** los textos de adentro de los modales y de las páginas de login y registro siguen solo en español.

---

## 4. Historial de cambios

| Fecha | Cambio |
| ----- | ------ |
| 20/11/2026 | Creación. Barra única en el molde; `header.css`; `lang/*/layout.php`; fuera "Crear CV" de la barra de la app |
