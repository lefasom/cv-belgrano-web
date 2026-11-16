# Comandos del proyecto: Tinker y Artisan

Chuleta de los comandos que se usan en **CV Belgrano Web**, ordenados por caso.
Los ejemplos usan las tablas reales: `users` y `cvs` (un CV = un perfil).

> ⚠️ Todo lo que se hace en Tinker **se guarda de verdad en MySQL**. No es una simulación.

---

## 1. Entrar y salir de Tinker

```powershell
php artisan tinker
```

```php
exit          // salir (o Ctrl + C)
```

Atajo para no escribir `App\Models\` cada vez (válido hasta salir):

```php
use App\Models\User;
use App\Models\Cv;
```

Los ejemplos de abajo dan por hecho que se escribieron esas dos líneas.

---

## 2. Ver datos (leer)

| Caso | Comando |
|---|---|
| Todos los usuarios | `User::all();` |
| Cuántos CV hay | `Cv::count();` |
| El primero | `Cv::first();` |
| Uno por su `id` | `Cv::find(1);` |
| Uno por `id`, y error si no existe | `Cv::findOrFail(1);` |
| Filtrar (`where` arma, `get` ejecuta) | `Cv::where('template', 'ats')->get();` |
| Filtrar y traer solo el primero | `User::where('email', 'leo@test.com')->first();` |
| Los más nuevos primero | `Cv::latest()->get();` |
| Solo una columna, como lista | `Cv::pluck('title');` |
| Buscar texto que contenga algo | `Cv::where('title', 'like', '%prueba%')->get();` |

---

## 3. Crear

```php
$user = User::first();

// Forma correcta: por la relación. Laravel completa user_id solo.
$cv = $user->cvs()->create([
    'title'     => 'Perfil administrativo',
    'full_name' => 'Leonardo Sombra',
    'email'     => 'leo@test.com',
]);
```

`title`, `full_name` y `email` son **obligatorios** (en la migración no tienen `->nullable()`).
Si falta uno: `Field 'email' doesn't have a default value`.

Crear un usuario de prueba:

```php
User::create(['name' => 'Prueba', 'email' => 'prueba@test.com', 'password' => 'secreto123']);
```

(La contraseña se guarda encriptada sola porque `User` la tiene como `hashed`.)

---

## 4. Modificar

```php
$cv = Cv::find(1);

// Opción A: varios campos de una vez (solo los que están en $fillable)
$cv->update(['headline' => 'Desarrollador web', 'location' => 'Buenos Aires']);

// Opción B: campo por campo y después guardar
$cv->phone = '11 1234-5678';
$cv->save();

// Modificar muchos de una vez (sin traerlos)
Cv::where('template', 'ats')->update(['template' => 'clasico']);
```

---

## 5. Borrar

| Caso | Comando | Qué pasa |
|---|---|---|
| Un CV | `Cv::find(1)->delete();` | Borra esa fila |
| Varios por un filtro | `Cv::where('title', 'like', '%prueba%')->delete();` | Borra los que coinciden |
| Los CV de un usuario | `User::find(1)->cvs()->delete();` | El usuario queda, sus CV no |
| Un usuario **y sus CV** | `User::find(2)->delete();` | Por la **cascada** (`cascadeOnDelete`), MySQL borra sus CV solo |
| **Vaciar** la tabla `cvs` | `Cv::truncate();` | Borra todas las filas y el `id` vuelve a 1. La tabla sigue existiendo |

> Cuando existan `experiences`, `education` y `skills` (que apuntan a `cvs`), `truncate()` va a fallar por las claves foráneas. Ahí conviene `Cv::query()->delete();` o `migrate:fresh` (ver §8).

---

## 6. Relaciones

| Caso | Comando |
|---|---|
| Los CV de un usuario | `User::first()->cvs;` |
| Cuántos CV tiene | `User::first()->cvs()->count();` |
| El dueño de un CV | `Cv::first()->user;` |
| Nombre del dueño | `Cv::first()->user->name;` |
| Filtrar dentro de sus CV | `User::first()->cvs()->where('template', 'ats')->get();` |
| Usuarios con sus CV (evita el N+1) | `User::with('cvs')->get();` |
| Solo usuarios que tienen al menos un CV | `User::has('cvs')->get();` |
| Usuarios con la cantidad de CV | `User::withCount('cvs')->get();` → campo `cvs_count` |

Diferencia clave:
- `cvs()` (con paréntesis) es la **consulta**: se puede seguir encadenando (`->where`, `->count`, `->create`).
- `cvs` (sin paréntesis) son los **datos** ya traídos.

---

## 7. Ver el SQL que arma Laravel

```php
Cv::where('user_id', 1)->toSql();
// "select * from `cvs` where `user_id` = ?"

DB::enableQueryLog();
User::with('cvs')->get();
DB::getQueryLog();   // muestra las consultas que se hicieron (acá: 2)
```

Sirve para entender el N+1 y para comparar Eloquent con SQL puro.

---

## 8. Comandos de Artisan para la base (fuera de Tinker)

Se escriben en la terminal normal, **no** dentro de Tinker.

### Mirar (no cambian nada)

| Comando | Para qué |
|---|---|
| `php artisan migrate:status` | Qué migraciones corrieron (`Ran`) y cuáles no |
| `php artisan db:show` | Datos de la conexión y lista de tablas |
| `php artisan db:table cvs` | Columnas, índices y claves foráneas de `cvs` |
| `php artisan model:show Cv` | Columnas, cuáles son `fillable` y relaciones del modelo |

### Cambiar la estructura

| Caso | Comando | ¿Borra datos? |
|---|---|---|
| Correr migraciones nuevas | `php artisan migrate` | No |
| Deshacer el **último lote** | `php artisan migrate:rollback` | **Sí**, de esas tablas |
| Deshacer solo la última migración | `php artisan migrate:rollback --step=1` | **Sí**, de esa tabla |
| Deshacer todo y volver a migrar (usa los `down()`) | `php artisan migrate:refresh` | **Sí, todo** |
| Borrar **todas** las tablas y migrar de cero (no usa los `down()`) | `php artisan migrate:fresh` | **Sí, todo** (también tu usuario) |
| Lo mismo y cargar datos de prueba | `php artisan migrate:fresh --seed` | **Sí, todo**, y después carga el seeder |
| Agregar una columna a `cvs` sin perder datos | `php artisan make:migration add_website_to_cvs_table` y después `migrate` | No |

### `up()` y `down()` de una migración

En `database/migrations/2026_11_15_233734_create_cvs_table.php`:

- `up()` → lo que hace `migrate`: **crea** la tabla (`Schema::create('cvs', ...)`).
- `down()` → lo que hace `rollback`: **la deshace** (`Schema::dropIfExists('cvs')` = "borrá la tabla `cvs` si existe", con sus datos).

Por eso `migrate:rollback` puede borrar la tabla `cvs` entera: ejecuta su `down()`.

**Regla:** una migración que **ya se subió a GitHub** no se edita. Para cambiar una tabla, se hace una migración nueva.

### Datos de prueba

| Comando | Para qué |
|---|---|
| `php artisan db:seed` | Corre `DatabaseSeeder` (carga datos sin borrar) |
| `php artisan make:seeder CvSeeder` | Crea un seeder nuevo |
| `php artisan make:factory CvFactory` | Crea una factory (ya existe la de `Cv`) |

---

## 9. Si algo no anda

| Síntoma | Comando |
|---|---|
| Cambié el `.env` y Laravel no se entera | `php artisan config:clear` |
| Algo raro con caché, rutas o vistas | `php artisan optimize:clear` |
| ¿Qué rutas existen? | `php artisan route:list` |
| Ver todos los comandos | `php artisan list` |
| Ayuda de un comando | `php artisan help migrate:fresh` |
