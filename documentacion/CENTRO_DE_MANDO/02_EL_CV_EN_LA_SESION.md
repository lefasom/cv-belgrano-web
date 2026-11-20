# 02_EL_CV_EN_LA_SESION

**Última actualización:** 20/11/2026

> Dónde vive el CV mientras se arma en `/crear` (todavía sin base de datos) y por qué hay una sola clase que lo maneja: `App\Cv\BorradorCv`.

---

## 1. Por qué los datos siguen ahí al recargar

No es la base de datos: es la **sesión de Laravel**.

1. Al entrar al sitio, Laravel le da al navegador una cookie con un número de sesión.
2. `.env` tiene `SESSION_DRIVER=file`: lo que se guarda con `session()->put(...)` va a un archivo en `storage/framework/sessions/`, asociado a ese número.
3. Al recargar, el navegador manda la misma cookie y Laravel lee el mismo archivo.

Se pierde si se borran las cookies o pasan `SESSION_LIFETIME` minutos (120 por defecto) sin usar el sitio. No hay tablas nuevas ni nada en MySQL.

| | Sesión (hoy) | Base de datos (después) |
| --- | --- | --- |
| Dónde | Archivo en `storage/framework/sessions` | Tablas `cvs`, `experiences`, `education`, `skills` |
| De quién | Del navegador (cookie) | Del usuario logueado (`user_id`) |
| Dura | Mientras dure la sesión | Para siempre |
| Necesita login | No | Sí |

Equivalente en la app: es como el `AsyncStorage` de `useCVStore`, pero guardado en el servidor.

---

## 2. Cómo queda guardado

Las claves usan los mismos nombres que las columnas de las tablas, para que guardarlo en la base después sea directo.

```php
session('cv') = [
    'datos' => ['full_name' => 'Ana López', 'headline' => '…', 'email' => '…',
                'phone' => '…', 'location' => '…', 'summary' => '…'],
    'experiencias' => [
        ['position' => '…', 'company_name' => '…', 'period' => '…', 'description' => "renglón 1\nrenglón 2"],
    ],
    'estudios' => [
        ['title_obtained' => '…', 'institution' => '…', 'period' => '…', 'status' => 'Completed'],
    ],
    'habilidades' => [['name' => 'Laravel'], ['name' => 'PHP']],
]
```

El punto en `session('cv.datos')` entra a un arreglo dentro de otro, como `config('app.name')`.

---

## 3. `BorradorCv`: los métodos

| Método | Qué hace | Lo usa |
| ------ | -------- | ------ |
| `todo()` | Devuelve las cuatro partes juntas | `vista-previa` |
| `datos()` / `guardarDatos($datos)` | Lee o guarda los datos personales (siempre con las 6 claves) | `datos` |
| `lista($nombre)` | Devuelve `experiencias`, `estudios` o `habilidades` | modales, `vista-previa` |
| `agregar($nombre, $item)` | Agrega al final (`session()->push`) | modales |
| `reemplazar($nombre, $i, $item)` | Cambia el ítem `$i` | experiencia, educación |
| `quitar($nombre, $i)` | Borra el ítem `$i` y corre los demás (`array_values`) | modales |
| `editar($campo, $texto)` | Edición desde el CV (ver abajo) | `vista-previa::editar` |
| `renglones($descripcion)` | Divide la descripción en viñetas: sin renglones vacíos ni guiones al principio | `DatosPlantilla`, `editar` |

---

## 4. Edición desde el CV: `editar($campo, $texto)`

Cada texto editable de las plantillas tiene un `data-cv-campo` que dice qué dato es:

| `data-cv-campo` | Cambia |
| --------------- | ------ |
| `datos.full_name` | El nombre |
| `experiencias.2.position` | El puesto de la tercera experiencia (se cuenta desde 0) |
| `experiencias.2.description.1` | El segundo renglón de la descripción de la tercera experiencia |
| `estudios.0.institution` | La institución del primer estudio |
| `habilidades.3.name` | La cuarta habilidad |

**Ese texto viene del navegador, así que no se le cree.** `editar()`:

1. Solo acepta los campos que están en la constante `EDITABLES`. Cualquier otro (por ejemplo `datos.user_id`) devuelve `false` y no cambia nada.
2. Junta los espacios y saltos de línea en uno solo (cada texto es de un renglón).
3. Respeta el largo máximo, el mismo que la validación de los modales.
4. No deja vacío un campo obligatorio (nombre, email, puesto…). Borrar todo el nombre no lo cambia.
5. Si es el email, tiene que ser un email válido.
6. En un renglón de la descripción, borrarlo entero lo quita, pero tiene que quedar al menos uno.

Si devuelve `false`, la vista previa se vuelve a dibujar con el texto de antes.

---

## 5. Por qué se dejó de usar `#[Session]` en cada componente

Hasta el 18/11 cada componente tenía su copia con `#[Session(key: …)]`. Livewire **lee** la sesión una sola vez, al montar el componente (`mount`), pero la **escribe** en cada petición (`dehydrate`). Con la edición sobre el CV pasaba esto:

```
1. Se abre la página: el modal "datos" carga su copia { full_name: 'Ana' }
2. Edito el nombre en el CV: la sesión queda en 'Ana María'
3. Abro el modal de datos: Livewire escribe SU copia vieja → la sesión vuelve a 'Ana'
```

Ahora ningún componente guarda una copia de los datos. Leen `BorradorCv` cuando lo necesitan (al abrir el modal o al dibujar) y escriben solo al guardar. Hay **una sola fuente de verdad**, la misma idea que un store único en React.

---

## 6. Para después: pasarlo a la base de datos

Cuando haya "Guardar en mi cuenta" (con login y `CvPolicy`), el camino natural es:

```php
$cv = auth()->user()->cvs()->create(BorradorCv::datos() + ['title' => '…']);
$cv->experiences()->createMany(BorradorCv::lista('experiencias'));
// … estudios y habilidades igual
```

Por eso las claves de la sesión ya son los nombres de las columnas.

---

## 7. Historial de cambios

| Fecha | Cambio |
| ----- | ------ |
| 20/11/2026 | Creación. Reemplaza el `#[Session]` por componente del 18/11 |
