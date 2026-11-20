# 01_ARQUITECTURA_DEL_CREADOR

**Última actualización:** 20/11/2026

> Mapa de la página `/crear`: qué archivo hace qué y cómo viaja un dato desde que lo escribís en un modal hasta que sale en el PDF.

---

## 1. La pantalla

```
┌──────────────── header (layouts/app.blade.php) ────────────────┐
│ CV Belgrano                          Iniciar sesión  Crear cuenta │
└──────────────────────────────────────────────────────────────────┘
┌── panel (izquierda) ──┐  ┌── vista previa (derecha) ─────────────┐
│ [Datos personales  ✓] │  │ [Plantilla ATS] [Personalizada]       │
│ [Experiencia       3] │  │ ┌──────────── ventana 90vh ─────────┐ │
│ [Educación         1] │  │ │  hoja A4 1                        │ │
│ [Habilidades       4] │  │ │  hoja A4 2 …                      │ │
│                       │  │ └───────────────────────────────────┘ │
│ HERRAMIENTAS          │  │ Tocá un texto…        [Descargar PDF] │
│  Foto · Letra · Zoom  │  └───────────────────────────────────────┘
└───────────────────────┘
```

En el celular (menos de 860 px) es una sola columna: el panel arriba y la vista previa abajo.

---

## 2. Qué archivo hace qué

### PHP (Laravel)

| Archivo | Qué hace |
| ------- | -------- |
| `routes/web.php` | `Route::view('crear', 'crear')`: la página es una vista Blade sin controlador |
| `resources/views/crear.blade.php` | Arma la página: el panel con los componentes Livewire y la vista previa |
| `app/Cv/BorradorCv.php` | **Único lugar** que lee y escribe el CV en la sesión. Ver `02` |
| `app/Cv/DatosPlantilla.php` | Prepara el CV para las plantillas: limpia y saca lo vacío. Ver `03` |
| `app/Cv/FotoCv.php` | Achica la foto con GD. Ver `05` |
| `app/Providers/AppServiceProvider.php` | Hace que `artisan serve` pase `TEMP`/`TMP` (para subir archivos en Windows) |

### Componentes Livewire (`resources/views/livewire/creador/`)

Son componentes de **archivo único**: arriba la clase PHP (`new class extends Component`) y abajo la vista.

| Componente | Estado (propiedades) | Métodos | Avisa con |
| ---------- | -------------------- | ------- | --------- |
| `datos` | `$borrador`, `$abierto` | `abrir`, `cerrar`, `guardar` | `cv-actualizado` |
| `experiencia` | `$borrador`, `$elegida`, `$abierto` | `abrir`, `cerrar`, `elegir`, `guardar`, `eliminar` | `cv-actualizado` |
| `educacion` | `$borrador`, `$elegido`, `$abierto` | igual que experiencia | `cv-actualizado` |
| `habilidades` | `$nueva`, `$abierto` | `abrir`, `cerrar`, `agregar`, `eliminar` | `cv-actualizado` |
| `foto` | `$archivo`, `$hayFoto` | `updatedArchivo`, `quitar` | `foto-cambiada` |
| `vista-previa` | `$plantilla`, `$foto` | `cv` (computada), `actualizar`, `ponerFoto`, `elegirPlantilla`, `editar` | — |

### Componentes Blade (`resources/views/components/`)

| Componente | Qué hace |
| ---------- | -------- |
| `cv/ats.blade.php` | Plantilla ATS (una columna, Arial, sin foto) |
| `cv/ejecutivo.blade.php` | Plantilla Personalizada (dos columnas, con foto) |
| `creador/selector.blade.php` | La fila "Experiencia 1, 2, 3… + Nueva" de los modales |

### CSS y JavaScript

| Archivo | Qué hace |
| ------- | -------- |
| `resources/css/crear.css` | Grilla de la página, herramientas, pestañas, ventana de la vista previa y **@media print** (lo que sale en el PDF) |
| `resources/css/creador/seccion.css` | Los cuatro botones de sección |
| `resources/css/creador/modal.css` | Modal, selector, lista de habilidades, botón eliminar |
| `resources/css/cv/hojas.css` | La hoja A4 y el resaltado de los textos editables (común a las plantillas) |
| `resources/css/cv/ats.css` · `cv/ejecutivo.css` | El diseño de cada plantilla |
| `resources/js/cv-paginado.js` | Paginación, edición, zoom/desplazar, letra y descarga. Ver `04` |

Todos los CSS y el JS están en el `input` de `vite.config.js`.

---

## 3. Cómo viaja un dato

Ejemplo: agregar la habilidad "Laravel".

```
1. Modal de habilidades       wire:submit="agregar"  → petición a PHP
2. habilidades::agregar()     valida y llama a BorradorCv::agregar('habilidades', ['name' => 'Laravel'])
3. BorradorCv                 session()->push('cv.habilidades', …)   → archivo en storage/framework/sessions
4. habilidades::agregar()     $this->dispatch('cv-actualizado')
5. vista-previa               #[On('cv-actualizado')] actualizar() → vuelve a dibujarse
6. vista-previa::cv()         BorradorCv::todo() → DatosPlantilla::desde() → <x-cv.ats :cv="…" />
7. Livewire                   cambia el HTML oculto de [data-cv-fuente]
8. cv-paginado.js             el MutationObserver lo nota → vuelve a armar las hojas A4
9. Descargar PDF              window.print() → @media print deja solo las hojas
```

La **edición sobre el CV** hace el camino al revés: `cv-paginado.js` llama a `editar()` de la vista previa, que pasa por `BorradorCv::editar()` y vuelve a dibujar.

La **foto** no pasa por la sesión: `foto` la achica y la manda con el evento `foto-cambiada` a `vista-previa`, que la guarda en su propiedad `$foto`.

---

## 4. Eventos entre componentes

| Evento | Lo manda | Lo escucha | Para qué |
| ------ | -------- | ---------- | -------- |
| `cv-actualizado` | `datos`, `experiencia`, `educacion`, `habilidades` (al guardar o eliminar) | `vista-previa` | Volver a leer la sesión y redibujar |
| `foto-cambiada` | `foto` (con `foto: "data:image/jpeg;base64,…"` o `null`) | `vista-previa` | Poner o quitar la foto |

Equivalente en React: es como levantar el estado o usar un *event bus*. Livewire lo resuelve con `$this->dispatch()` y `#[On(...)]`.

---

## 5. Historial de cambios

| Fecha | Cambio |
| ----- | ------ |
| 20/11/2026 | Creación |
