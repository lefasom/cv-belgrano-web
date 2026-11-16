# Retomar: CV Belgrano Web

Nota para volver a empezar después de una pausa. Última actualización: **16/11/2026**.

---

## 1. Al terminar el día (apagar lo que está encendido)

| Qué | ¿Hay que apagarlo? | Cómo |
|---|---|---|
| **Vite** (`composer run dev` o `npm run dev` en una terminal) | Sí, si quedó abierto | En esa terminal: **Ctrl + C** (si pregunta "¿terminar el trabajo por lotes?", responder `S`) |
| **Tinker** (`php artisan tinker`) | Sí, si quedó abierto | Escribir `exit` |
| **Herd** (sirve `cv-belgrano-web.test`) | No hace falta. Consume poco | Para cerrarlo: ícono de Herd junto al reloj → **Quit** |
| **MySQL** (servicio `MySQL2610`) | No hace falta. Arranca solo con la PC y consume poco | Para apagarlo: tecla Windows → **"Servicios"** → `MySQL2610` → clic derecho → **Detener**. O en PowerShell **como administrador**: `Stop-Service MySQL2610` |

Si se apaga la PC, se apaga todo solo. Al encenderla, **MySQL vuelve a arrancar solo** (está en modo Automático).

---

## 2. Al empezar de nuevo (lanzar el proyecto)

**1. Comprobar que MySQL está encendido:**

```powershell
Get-Service MySQL2610
```

Tiene que decir `Running`. Si dice `Stopped`: Servicios → `MySQL2610` → **Iniciar**, o en PowerShell como administrador `Start-Service MySQL2610`.

**2. Abrir Herd** (menú Inicio → Herd), si no está abierto.

**3. Abrir el proyecto en VS Code:**

```powershell
cd C:\Users\leo\Herd\cv-belgrano-web
code .
```

**4. Levantar Vite** (CSS y JS) en una terminal de VS Code, y dejarla abierta:

```powershell
npm run dev
```

**5. Abrir la web:** http://cv-belgrano-web.test
(Alternativa sin Herd: `php artisan serve` en otra terminal → http://localhost:8000)

**6. Comprobar que Laravel ve la base:**

```powershell
php artisan migrate:status
```

Tienen que aparecer las 4 migraciones como `Ran` (users, cache, jobs y cvs).

---

## 3. Dónde quedamos (16/11/2026)

### Hecho
- Entorno: Herd (PHP 8.5, Composer, Laravel), MySQL 26.10 local, Node.
- Proyecto Laravel 13 + starter kit de Livewire, conectado a MySQL (`cv_belgrano_web`). Registro y login andando.
- Repo público: https://github.com/lefasom/cv-belgrano-web.
- Tabla **`cvs`** con clave foránea `user_id` → `users` (cascada). Un "CV" es un **perfil** (Manual §0).
- Entendido: Herd contra `php artisan serve`, qué es Artisan, Composer contra npm, `APP_URL`, `migrate` (Manual §3 y §13).
- **Relaciones escritas por Leo**: `$fillable` y `user()` en `Cv.php`, `cvs()` en `User.php`. Probadas con Tinker (`all`, `first`, `find`, `where`, `get`, `create`). Hay 1 CV de prueba en la base.

### ⚠️ Arreglar primero (revisión del 16/11)
1. **`Cv.php` no compila la relación**: falta arriba `use Illuminate\Database\Eloquent\Relations\BelongsTo;`.
   Hoy `App\Models\Cv::first()->user` da `TypeError ... must be of type App\Models\BelongsTo`. `$user->cvs` sí anda.
2. **Sacar `user_id` de `$fillable`** y crear siempre con `$user->cvs()->create([...])` (Manual §11 y §13).
3. **`.env`**: se deja `APP_URL=http://localhost:8000` porque Leo trabaja con `php artisan serve` desde VS Code. Si pasa a usar Herd, cambiarlo a `http://cv-belgrano-web.test`. Tiene que coincidir con la puerta que se usa.
4. `comandos.tinker.md` (nuevo, sin commit): decidir si se completa o se borra; lo mismo ya está en Manual §13.
5. Probar en Tinker las dos direcciones y hacer **commit + push**:
   ```powershell
   git add .
   git commit -m "Relaciones User-Cv, fillable y Manual de la sesión"
   git push
   ```

### Próximos pasos, en orden
1. **Factory y seeder**: completar `CvFactory::definition()` con `fake()`, y en `DatabaseSeeder` crear un usuario con 3 perfiles: `User::factory()->has(Cv::factory()->count(3))->create()`. Correr `php artisan migrate:fresh --seed` (entender que **borra todo** y vuelve a crear).
2. **Tablas hijas del perfil**: `experiences`, `education`, `skills` (FK a `cvs` con `cascadeOnDelete`, `sort_order`, índice en `skills.name`) y sus relaciones `hasMany` en `Cv`.
3. **Primera pantalla: "Mis perfiles"**: ruta protegida con `auth`, componente Livewire (o controlador + Blade) que lista `auth()->user()->cvs`. Ver el N+1 y arreglarlo con `with()`.
4. **Crear / editar / borrar perfil** (CRUD): validación de formularios y **Policy** para que nadie edite perfiles ajenos.
5. **Plantilla ATS en Blade + PDF con dompdf**.
6. **Tests con Pest** de lo anterior (ya vienen instalados).
7. **Landing** que empuja a bajar la app, y **subir a un hosting**.

---

## 4. Mensaje para empezar una sesión nueva (con Claude o con otra IA)

Copiar y pegar:

```text
Estoy aprendiendo Laravel con un proyecto real: CV Belgrano Web, la versión web de mi app de CV
(React Native). Es para postular a un puesto Junior PHP/Laravel/MySQL (Blade + Livewire) que
cierra el 02/01/2027. Proyecto: Laravel 13 + starter kit Livewire 4 + Flux, Pest, MySQL local,
Herd en Windows. Carpeta: C:\Users\leo\Herd\cv-belgrano-web.

Cómo quiero trabajar:
- YO escribo todo el código y hago los commits. Vos explicás y revisás. No me des archivos
  completos para copiar: decime qué va, dónde y por qué, y después revisás lo que escribí.
- Antes de cada concepto nuevo, explicámelo simple (analogía o tabla, comparando con
  JavaScript/React/Firebase, que ya conozco). Me gusta entender el entorno y las herramientas.
- Todo en español.

Dónde estoy (16/11/2026):
- Hecho: login/registro, tabla cvs (un "CV" = un perfil de datos; un usuario tiene muchos)
  con user_id → users en cascada. Modelos: Cv tiene $fillable y user() (belongsTo);
  User tiene cvs() (hasMany). Probé Tinker: all, first, find, where, get, create.
- Entendí: Artisan, Composer vs npm, migrate, APP_URL, Herd vs php artisan serve.
- Para arreglar primero:
  1) En Cv.php falta "use Illuminate\Database\Eloquent\Relations\BelongsTo;" ($cv->user da TypeError).
  2) Sacar user_id de $fillable y crear con $user->cvs()->create([...]) (explicame por qué).
  3) APP_URL queda en http://localhost:8000: trabajo con php artisan serve (no con Herd).
  4) Commit y push.

Qué sigue, en este orden (enseñame paso a paso):
1) CvFactory con fake() + DatabaseSeeder + migrate:fresh --seed.
2) Tablas experiences, education, skills con FK a cvs, cascada, sort_order; relaciones hasMany.
3) Pantalla "Mis perfiles" con Livewire, ruta con middleware auth; ver el problema N+1 y with().
4) CRUD de perfiles con validación y Policy (que nadie toque perfiles ajenos).
5) Plantilla ATS en Blade y PDF con dompdf.
6) Tests con Pest.
7) Deploy en un hosting barato que no se duerma.

Al terminar cada tema, dame un resumen corto para agregar a mi Manual.md.
```
