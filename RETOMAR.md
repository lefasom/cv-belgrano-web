# Retomar: CV Belgrano Web

Nota para volver a empezar después de una pausa. Última actualización: **15/11/2026**.

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

## 3. Dónde quedamos

### Hecho
- Entorno: Herd (PHP 8.5, Composer, Laravel), MySQL 26.10 local, Node.
- Proyecto Laravel 13 + starter kit de Livewire, conectado a MySQL (`cv_belgrano_web`).
- Registro y login funcionando.
- Repo público: https://github.com/lefasom/cv-belgrano-web (2 commits).
- Tabla **`cvs`** creada, con clave foránea `user_id` → `users` (cascada).
- **Decisión de diseño:** un "CV" es un **perfil** (un juego de datos). Un usuario tiene muchos perfiles; el diseño vive en el código (ver `Manual.md` §0).
- `Manual.md` con todo lo aprendido (secciones 0 a 12).

### Pendiente antes de seguir
- [ ] **Commit del `Manual.md`** (tiene cambios sin guardar en Git) y de este archivo:
  ```powershell
  git add .
  git commit -m "Manual: relaciones, SQL contra NoSQL, decisión de perfiles y nota para retomar"
  git push
  ```

### Próximo paso: el ejercicio de las relaciones (Manual §11)

Todavía **no está escrito**. Lo escribe Leo:

1. **`app/Models/Cv.php`**, debajo de `use HasFactory;`:
   - `protected $fillable = [...]` con: `title`, `full_name`, `headline`, `email`, `phone`, `location`, `summary`, `template` (sin `id`, `user_id` ni fechas).
   - Método `user()` que devuelve `$this->belongsTo(User::class);` (con `use Illuminate\Database\Eloquent\Relations\BelongsTo;` arriba).
2. **`app/Models/User.php`** (no borrar nada, solo agregar): método `cvs()` que devuelve `$this->hasMany(Cv::class);` (con `use Illuminate\Database\Eloquent\Relations\HasMany;` arriba).
3. **Probar con Tinker:**
   ```powershell
   php artisan tinker
   ```
   ```php
   $user = App\Models\User::first();
   $user->cvs()->create(['title' => 'Perfil prueba', 'full_name' => 'Leo', 'email' => 'leo@test.com']);
   $user->cvs;
   App\Models\Cv::first()->user->name;
   exit
   ```

### Después
- Tablas `experiences`, `education` y `skills` (con índice en `skills.name`).
- `CvController`, Form Requests y `CvPolicy` → pantalla "Mis perfiles".
- Ver el 1+N en vivo en esa pantalla.

---

## 4. Mensaje para empezar una sesión nueva con Claude

Copiar y pegar:

```text
Retomo CV Belgrano Web (Laravel). El proyecto está en C:\Users\leo\Herd\cv-belgrano-web.
Leé RETOMAR.md y Manual.md de esa carpeta, y el plan en
C:\Users\leo\Desktop\App-de-cv\CV_BELGRANO_WEB_LARAVEL\.
Primero verificá que MySQL y la web andan (sección 2 de RETOMAR.md).
Seguimos con el ejercicio de las relaciones (Manual §11): yo escribo el código y vos me explicás
y revisás. Explicame cada concepto antes de usarlo y sumá lo nuevo al Manual.md.
```
