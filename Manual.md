# Manual de estudio: CV Belgrano Web

Manual personal para entender **cada tecnología y cada comando** que se usa en este proyecto. Se va completando a medida que avanza el proyecto. La idea: primero entender la herramienta, después usarla.

| Dato | Valor |
|---|---|
| Proyecto | **CV Belgrano Web** (versión web de la app CV Belgrano) |
| Carpeta en la PC | `C:\Users\leo\Herd\cv-belgrano-web` |
| Repositorio | https://github.com/lefasom/cv-belgrano-web (público) |
| Dirección local | http://cv-belgrano-web.test (la sirve Herd) |
| Base de datos local | MySQL, base `cv_belgrano_web` |
| Plan y estrategia | Repo de la app, carpeta `CV_BELGRANO_WEB_LARAVEL/` |

---

## 1. El mapa: qué es cada pieza

| Pieza | Qué es | Analogía |
|---|---|---|
| **PHP** | El lenguaje de programación del backend | El idioma |
| **Laravel** | Un **framework** de PHP: un proyecto ya armado con carpetas, reglas y herramientas para hacer webs | Una casa con cimientos, cañerías y luz ya puestos |
| **Composer** | Instala librerías de PHP (Laravel mismo, dompdf, etc.). Lee `composer.json` | Como `npm` en JavaScript |
| **Artisan** | La "herramienta de comandos" de Laravel: `php artisan ...` | Como `ng` en Angular o `expo` en la app |
| **MySQL** | El **servidor de base de datos**: un programa que está siempre encendido, guarda las tablas y responde consultas SQL | El depósito con un encargado que atiende pedidos |
| **Herd** | El instalador que trajo PHP, Composer y el comando `laravel`. También sirve la web en `*.test` | La caja de herramientas |
| **Node + Vite** | Compilan el CSS y el JavaScript del frontend | Igual que en cualquier proyecto web moderno |
| **Livewire** | Componentes PHP que actualizan la página sin recargarla | "React, pero escrito en PHP" |
| **Blade** | Las plantillas HTML de Laravel (`*.blade.php`) | El HTML con superpoderes |
| **Git / GitHub** | Historial de cambios / dónde se publica el código | — |

### Frontend, backend y base de datos en este proyecto

| Parte | Qué hace | Dónde está |
|---|---|---|
| **Frontend** | Lo que ve el usuario: pantallas, formularios, estilos | `resources/views`, `resources/css`, `resources/js` |
| **Backend** | La lógica: modelos, controladores, permisos, validaciones | `app/` |
| **Base de datos** | El diseño de las tablas y sus relaciones | `database/migrations` → se crean en MySQL |

Laravel es principalmente de **backend**, pero cubre las dos partes (*full-stack*) gracias a Blade y Livewire.

### MySQL, XAMPP y lo que usamos nosotros

MySQL es un **servidor**: un programa que está **vivo**, escuchando pedidos y respondiendo. No es un archivo quieto. Funciona igual que un servidor online, pero en tu PC (local).

**XAMPP** es un "paquete todo en uno" que instala juntos:
- **Apache**: el servidor web, el que entrega las páginas.
- **MySQL** (en realidad MariaDB, compatible).
- **PHP**.
- **phpMyAdmin**: una página web para ver y editar las tablas.

Nosotros usamos lo mismo, pero en piezas separadas y más actuales:

| En XAMPP | En este proyecto |
|---|---|
| Apache | **nginx**, que viene dentro de Herd |
| PHP | **PHP 8.5**, de Herd |
| MySQL / MariaDB | **MySQL Community Server 26.10**, instalado aparte |
| phpMyAdmin | Opcional: **MySQL Workbench** o **HeidiSQL** (programas de escritorio para ver las tablas). Todavía no instalado |

#### Dónde vive MySQL en la PC

| Qué | Dónde |
|---|---|
| El programa | `C:\Program Files\MySQL\MySQL Server 26.10\` |
| El **servicio de Windows** (el que lo mantiene encendido) | `MySQL2610`, arranca solo con la PC |
| Los **datos** (los archivos reales de las tablas) | `C:\ProgramData\MySQL\MySQL Server 26.10\Data\` (carpeta oculta). Cada base es una subcarpeta, por ejemplo `cv_belgrano_web` |
| Dirección para conectarse | `127.0.0.1` (esta misma PC), puerto `3306` |

Esos archivos **no se tocan a mano**: siempre se trabaja a través de MySQL (comandos SQL, Laravel o un programa como Workbench).

#### ¿Cuándo se encendió el servidor?

En la **instalación**, sin darnos cuenta. En el **MySQL Configurator**:
1. Se marcó **"Configure MySQL Server as a Windows Service"**: quiere decir "registrá MySQL como servicio de Windows".
2. En **"Apply Configuration"** se tocó **Execute**: ahí se creó la carpeta de datos, se guardó la contraseña de root y **se arrancó el servicio por primera vez**.
3. Quedó en modo **Automatic**: **se prende solo cada vez que encendés la PC**. No hay que hacer nada.

Vive **en tu PC**: usa tu procesador, tu memoria y tu disco. Nadie de afuera puede entrar.

Cómo verlo, prenderlo o apagarlo:

| Qué | Cómo |
|---|---|
| Ver si está encendido | Tecla Windows → **"Servicios"** → buscar `MySQL2610` → columna "Estado" |
| Desde PowerShell | `Get-Service MySQL2610` (dice `Running` o `Stopped`) |
| Apagarlo / prenderlo | En "Servicios": clic derecho → Detener / Iniciar. O en PowerShell **como administrador**: `Stop-Service MySQL2610` / `Start-Service MySQL2610` |

Si un día Laravel dice *"No connection could be made"* o *"Connection refused"*, lo primero es revisar que este servicio esté encendido.

### ¿Y el archivo `database/database.sqlite`?

**SQLite** es una base de datos que vive **en un solo archivo**, sin servidor. Es útil para pruebas, apps de celular o de escritorio.

- **Por qué está:** el instalador de Laravel arranca por defecto con SQLite y creó ese archivo. **No elegiste mal:** después lo cambiamos a MySQL en el `.env`.
- **Cómo saber cuál se usa:** lo decide la línea `DB_CONNECTION` del `.env`. Hoy dice `mysql`. El comando `php artisan db:show` lo confirma: muestra `MySQL 26.10` y la base `cv_belgrano_web`.
- **¿Se sube a GitHub?** No. El archivo `database/.gitignore` dice `*.sqlite*`, así que Git lo ignora.
- **¿Se puede borrar?** Sí, no se usa. También puede quedar ahí sin molestar.
- **Dónde sí se usa SQLite:** en las **pruebas automáticas** (más adelante). Se crea una base en memoria que desaparece al terminar, para que las pruebas sean rápidas y no ensucien tu MySQL.

---

## 0. Decisión de diseño: qué es "un CV" en esta web (15/11/2026)

Igual que en la app CV Belgrano:

- Un **CV** (fila de la tabla `cvs`) es en realidad un **perfil**: un juego de datos (nombre, titular, resumen, experiencias, estudios, habilidades). La tabla mantiene el nombre `cvs`.
- **Un usuario tiene muchos perfiles** (`User hasMany Cv`). Ejemplo: "Perfil desarrollador" y "Perfil soporte técnico", cada uno con sus propias habilidades.
- **El diseño no vive en la base de datos: vive en el código** (las plantillas Blade). Cualquier perfil se puede mostrar con **cualquier diseño** en cualquier momento.
- La columna `template` solo **recuerda el último diseño elegido** para ese perfil (por defecto `ats`). No ata el perfil a un diseño.

```text
users ──< cvs (perfiles) ──< experiences
                         ├──< education
                         └──< skills

Plantillas (código) ── se aplican a ──> cualquier perfil
```

---

## 1-bis. PHP para quien viene de JavaScript

| JavaScript | PHP | Qué es |
|---|---|---|
| `let cv = ...` | `$cv = ...` | Las variables llevan **`$` adelante** |
| `cv.title` | `$cv->title` | La **flechita `->`** es el **punto** de JavaScript: "entrá a una propiedad o método del objeto" |
| `cv.save()` | `$cv->save()` | Llamar a un método |
| `Cv.find(1)` (estático) | `Cv::find(1)` | Los **dos puntos dobles `::`** se usan con la **clase**, no con un objeto |
| `'a' + 'b'` | `'a' . 'b'` | Unir textos usa **punto** |
| `{ title: 'x' }` | `['title' => 'x']` | Objeto/arreglo con claves: `=>` en vez de `:` |
| `for (const cv of cvs)` | `foreach ($cvs as $cv)` | Recorrer una lista |
| `console.log(x)` | `dump($x)` o `dd($x)` | Mostrar algo para depurar (`dd` = mostrar y frenar) |

Ejemplo leído en voz alta: `$cv->user->name` = "del CV, andá a su usuario, y del usuario, dame el nombre". En JavaScript sería `cv.user.name`.

---

## 2. El archivo `.env`

Es la **configuración privada** del proyecto: contraseñas y datos que cambian entre tu PC y el servidor.

```env
APP_NAME="CV Belgrano Web"

DB_CONNECTION=mysql        # qué motor de base usar
DB_HOST=127.0.0.1          # dónde está el servidor (127.0.0.1 = esta PC)
DB_PORT=3306               # el "puerto" donde escucha MySQL
DB_DATABASE=cv_belgrano_web
DB_USERNAME=root
DB_PASSWORD="..."          # entre comillas si tiene símbolos como @ o #
```

- **Nunca se sube a GitHub** (está en `.gitignore`). En su lugar se sube `.env.example`, que es la misma lista **sin** contraseñas.
- Cuando publiques la web, el servidor va a tener **su propio `.env`** con los datos de su base. El código es el mismo; solo cambia este archivo.

---

## 3. Comandos usados hasta ahora (en orden)

### Verificar las herramientas

```powershell
php -v              # versión de PHP
composer -V         # versión de Composer
laravel --version   # versión del instalador de Laravel
mysql --version     # versión del cliente de MySQL
```

### Crear el proyecto

```powershell
cd C:\Users\leo\Herd
laravel new cv-belgrano-web
```

Baja el "esqueleto" de Laravel (el repositorio base `laravel/laravel` de GitHub; por eso en algunos videos dicen "clonar el repositorio de Laravel"), instala las librerías con Composer y con npm, y hace preguntas:

| Pregunta | Respuesta elegida | Por qué |
|---|---|---|
| Starter kit | Livewire | Es lo que usa la empresa del aviso |
| Autenticación | Laravel built-in | No depende de un servicio externo (WorkOS) |
| Single-file components | No | Clase PHP y vista separadas: se ve mejor el MVC |
| Funciones de login | `registration,password-confirmation` | Lo mínimo para que cualquiera se registre sin trabas |
| Boost | Se salió con Ctrl+C | Opcional, se puede configurar después |
| Pruebas | Quedó **Pest** | Funciona encima de PHPUnit, más corto de escribir |

### Crear la base de datos en MySQL

```powershell
mysql -u root -p -e "CREATE DATABASE cv_belgrano_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

| Parte | Significado |
|---|---|
| `mysql` | El programa cliente para hablar con el servidor MySQL |
| `-u root` | Usuario `root` (el administrador) |
| `-p` | "Pedime la contraseña" (no se ve mientras se escribe) |
| `-e "..."` | Ejecutá este comando SQL y salí |
| `CREATE DATABASE cv_belgrano_web` | Crea una base vacía (un "contenedor" de tablas) |
| `utf8mb4` | Guarda cualquier carácter: acentos, ñ, emojis |
| `utf8mb4_unicode_ci` | Cómo compara y ordena textos (`ci` = no distingue mayúsculas) |

### Ver con qué base está conectado Laravel

```powershell
php artisan db:show          # motor, base, cantidad de tablas
php artisan db:table cvs     # columnas, tipos, índices y claves foráneas de una tabla
```

`db:table` sirve para **comprobar que la migración creó lo que querías**. Para `cvs` muestra, por ejemplo:
- `id` → `bigint unsigned` autoincremental (clave primaria)
- `user_id` → índice `cvs_user_id_foreign` y clave foránea a `users.id` con **On Delete: cascade**
- `headline`, `phone`, `location`, `summary` → `nullable`
- `template` → valor por defecto `ats`
- Motor **InnoDB**: el motor de MySQL que soporta claves foráneas y transacciones

### Levantar la web

Con Herd no hace falta: entrar a http://cv-belgrano-web.test.
Alternativa sin Herd: `php artisan serve` → http://localhost:8000.
Para que el CSS y el JS se actualicen solos mientras programás: `composer run dev` (o `npm run dev`).

### Git

```powershell
git init -b main                 # convierte la carpeta en un repositorio, rama "main"
git add .                        # prepara todos los cambios
git commit -m "mensaje"          # guarda una "foto" del proyecto
git remote add origin https://github.com/lefasom/cv-belgrano-web.git   # conecta con GitHub
git push -u origin main          # sube los commits a GitHub
git status                       # qué cambió desde el último commit
git restore <archivo>            # recupera un archivo como estaba en el último commit
```

`git restore` solo recupera archivos que **ya estaban en un commit**. Un archivo nuevo que nunca se guardó en Git, si se borra, se pierde.

---

## 4. `php artisan make:model Cv -mf` en detalle

Es como `ng generate` en Angular: **genera archivos con la estructura básica**, listos para completar. **No toca la base de datos:** solo crea archivos.

| Parte | Significado |
|---|---|
| `php artisan` | Ejecutá la herramienta de comandos de Laravel |
| `make:model` | Creá un modelo |
| `Cv` | Nombre del modelo: en **singular** y con mayúscula inicial |
| `-m` | Creá también la **migración** |
| `-f` | Creá también la **factory** |

Otras letras útiles: `-s` (seeder), `-c` (controlador), `-p` (policy), `-a` (todo junto).

### Archivo 1: el modelo, `app/Models/Cv.php`

```php
class Cv extends Model
{
    use HasFactory;
}
```

- Es una **clase PHP** que representa **un CV**. Cada fila de la tabla `cvs` se convierte en un objeto `Cv`.
- `extends Model` hereda todo lo de **Eloquent** (el sistema de Laravel para hablar con la base sin escribir SQL): `Cv::all()`, `Cv::find(1)`, `$cv->save()`, `$cv->delete()`.
- **Convención de nombres:** el modelo `Cv` (singular) usa automáticamente la tabla `cvs` (plural). No hace falta decírselo.
- `use HasFactory` habilita `Cv::factory()` (ver archivo 3).
- **Lo que falta escribir acá:** las **relaciones** (`belongsTo(User)`, `hasMany(Experience)`...) y qué campos se pueden llenar desde un formulario (`$fillable`).

### Archivo 2: la migración, `database/migrations/2026_11_15_233734_create_cvs_table.php`

```php
public function up(): void      // qué hacer al migrar
{
    Schema::create('cvs', function (Blueprint $table) {
        $table->id();
        // ... columnas ...
        $table->timestamps();
    });
}

public function down(): void    // cómo deshacerlo
{
    Schema::dropIfExists('cvs');
}
```

- Es el **plano de una tabla** escrito en PHP. Laravel lo traduce a SQL (`CREATE TABLE cvs (...)`).
- **`up()`** crea. **`down()`** deshace (borra la tabla). Así cada cambio se puede ir y volver.
- **La fecha del nombre** define el **orden** en que se ejecutan. Las de Laravel tienen fecha `0001_01_01` para correr siempre primero. `cvs` tiene que ir después de `users` porque **depende** de ella (`user_id`).
- Ventaja frente a crear tablas a mano: el diseño de la base **queda en el código**, viaja por Git y se recrea igual en cualquier PC o servidor.

| Código | Qué hace | En MySQL |
|---|---|---|
| `$table->id()` | Número único de cada fila | `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` |
| `foreignId('user_id')->constrained()` | Conecta con la tabla `users` | **Clave foránea** + índice automático |
| `->cascadeOnDelete()` | Si se borra el usuario, se borran sus CV | `ON DELETE CASCADE` |
| `string('title')` | Texto corto | `VARCHAR(255)` |
| `text('summary')` | Texto largo | `TEXT` |
| `->nullable()` | Puede quedar vacío | permite `NULL` |
| `->default('ats')` | Valor por defecto | `DEFAULT 'ats'` |
| `timestamps()` | Fechas de creación y modificación | `created_at`, `updated_at` |

### Archivo 3: la factory, `database/factories/CvFactory.php`

```php
public function definition(): array
{
    return [
        //
    ];
}
```

- Una **fábrica de CV de mentira** para pruebas y datos de ejemplo.
- Dentro de `definition()` se dice cómo inventar cada campo, por ejemplo `'full_name' => fake()->name()`.
- Se usa así: `Cv::factory()->count(5)->create()` → crea 5 CV inventados en la base.
- Sirve para: las **pruebas automáticas** y el **usuario demo** (seeder) que va a ver el reclutador.

### Cómo se relacionan los 3

| Archivo | Pregunta que responde | ¿Toca la base? |
|---|---|---|
| Migración | ¿**Cómo es** la tabla? | Sí, cuando corrés `migrate` |
| Modelo | ¿**Cómo uso** esa tabla desde PHP? | Sí, cuando el código lee o guarda datos |
| Factory | ¿Cómo **invento** datos de prueba? | Solo cuando se la llama |

---

## 5. `php artisan migrate` en detalle

**Solo usa las migraciones.** El modelo y la factory no intervienen.

Paso a paso, cuando lo corrés:

1. Lee el `.env` para saber **a qué base conectarse** (MySQL, `cv_belgrano_web`).
2. Se conecta al **servidor MySQL**.
3. Mira la tabla especial **`migrations`**, donde Laravel anota qué migraciones ya corrió.
4. Busca en `database/migrations` las que **todavía no corrieron**.
5. Ejecuta el `up()` de cada una, **en orden de fecha**. Cada `up()` se traduce a SQL (`CREATE TABLE ...`).
6. Anota cada una en la tabla `migrations` con un número de **lote** (*batch*).

Las tablas **no se crean en una carpeta del proyecto**: se crean **dentro del servidor MySQL**, en la base `cv_belgrano_web`.

### Las migraciones que trae Laravel

Vienen **por defecto** con todo proyecto nuevo (fecha `0001_01_01`). Ojo: el nombre del archivo no es el nombre de la tabla, y **cada migración crea varias tablas**:

| Migración | Tablas que crea | Para qué sirven |
|---|---|---|
| `create_users_table` | `users` | Los usuarios registrados (nombre, email, contraseña cifrada) |
| | `password_reset_tokens` | Códigos temporales para "olvidé mi contraseña" |
| | `sessions` | Quién tiene la sesión iniciada (así Laravel recuerda que ya entraste) |
| `create_cache_table` | `cache`, `cache_locks` | Guardar resultados temporales para no recalcularlos |
| `create_jobs_table` | `jobs`, `job_batches`, `failed_jobs` | Tareas en segundo plano (ej.: mandar un mail sin hacer esperar al usuario) |

No se borran. Las del proyecto (`cvs`, `experiences`, etc.) se **suman** al lado.

### La tabla `users` y las opciones elegidas al crear el proyecto

La tabla `users` **es siempre la misma** en todo proyecto Laravel, elijas lo que elijas:

| Columna | Para qué |
|---|---|
| `id` | Número único |
| `name` | Nombre |
| `email` | Email, **único** (`unique()`: no puede haber dos iguales; también crea un índice) |
| `email_verified_at` | Fecha en que verificó el email. **Viene siempre**, aunque no se haya activado la verificación: queda vacía (`NULL`) |
| `password` | Contraseña **cifrada** (nunca se guarda tal cual) |
| `remember_token` | Para el "Recordarme" del login |
| `created_at`, `updated_at` | Fechas |

Lo que se eligió al crear el proyecto (`registration,password-confirmation`) **no cambió esta tabla**. Cambió:
- **`config/fortify.php`**, en `'features'`: hoy dice `registration()` y `resetPasswords()`. *Fortify* es el paquete de Laravel que maneja el login por detrás.
- **Qué pantallas y rutas existen** (registro, confirmar contraseña).

Si se hubiera elegido:
- **Two-factor (2FA):** se agregaba **otra migración** que **suma columnas** a `users` (`two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`).
- **Passkeys:** se agregaba **una tabla nueva** para guardar las llaves de cada usuario.

Esto muestra cómo se agrega algo a una tabla que ya existe: **no se edita la migración original**, se crea **una nueva** que modifica la tabla (sección 5, "Regla de oro").

### Comandos de migraciones

| Comando | Qué hace | ¿Borra datos? |
|---|---|---|
| `php artisan migrate` | Corre las migraciones pendientes | No |
| `php artisan migrate:status` | Lista cada migración: `Ran` (corrida) o `Pending` (pendiente) y su lote | No |
| `php artisan migrate:rollback` | Deshace el **último lote** (corre los `down()`) | Sí, las tablas de ese lote |
| `php artisan migrate:fresh` | Borra **todas** las tablas y corre todo de nuevo | **Sí, todo** |
| `php artisan migrate:fresh --seed` | Lo mismo + carga los datos de ejemplo | **Sí, todo** |

**Regla de oro:** una migración que ya corrió **no se edita**, salvo que primero la deshagas (`rollback`). En un proyecto que ya está online con datos reales, en vez de editar se crea **una migración nueva** que modifica la tabla.

---

## 6. Errores que aparecieron y cómo se resolvieron

| Error | Causa | Solución |
|---|---|---|
| `ERROR 1045 Access denied for user 'root'` | Contraseña de MySQL mal escrita | Escribirla de nuevo con cuidado (no se ve al tipear) |
| `ERROR 1007 Can't create database ... database exists` | La base ya se había creado en el intento anterior | Nada: ya estaba creada |
| `¿Desea terminar el trabajo por lotes (S/N)?` | Mensaje de Windows después de Ctrl+C | Responder `S` o abrir otra terminal |
| Se borraron `User.php` y las migraciones de Laravel | Se pensó que sobraban | `git restore ...`. **Los archivos de Laravel no se borran: los tuyos se suman al lado** |
| Dos migraciones de `cvs` | Se corrió `make:model` dos veces | Borrar una. Si corren las dos, la segunda falla con "la tabla ya existe" |

---

## 7. ¿Por qué Laravel y MySQL? (comparado con React, Firebase y MongoDB)

### Primero: Laravel y React no compiten

| | Qué es | Dónde trabaja |
|---|---|---|
| **React** | Una **librería de frontend** | En el **navegador**: dibuja la pantalla |
| **Laravel** | Un **framework de backend** (con frontend incluido) | En el **servidor**: datos, login, permisos, PDF |

Se pueden usar **juntos**: Laravel tiene un starter kit con React (vía *Inertia*). La comparación justa es Laravel contra **Node/Express o Next.js** en el backend, y MySQL contra **Firebase o MongoDB** en la base.

### Ventajas de Laravel

| Ventaja | Qué quiere decir |
|---|---|
| **Viene con todo** | Login, validación de formularios, permisos, base de datos, migraciones, mails, colas, pruebas. En Node cada cosa es una librería distinta que hay que elegir y conectar |
| **Convenciones** | Cada cosa tiene su lugar (modelos en `app/Models`, tablas en `migrations`...). Cualquier programador de Laravel entiende un proyecto ajeno rápido, ideal para trabajar en equipo y mantener código viejo |
| **Seguridad por defecto** | Contraseñas cifradas, protección contra formularios falsos (CSRF) y contra inyección SQL, sin configurar nada |
| **Hosting barato y en todos lados** | PHP corre en casi cualquier hosting, incluso los más económicos |
| **Muy productivo para sistemas de gestión** | Usuarios, registros, permisos, reportes y formularios: es justo lo que hacen las empresas como Nodo Austral |
| **Maduro y estable** | Más de 10 años, mucha documentación y una comunidad grande. En Argentina hay mucho trabajo en PHP para pymes |

**Desventajas** (para ser justos): una pantalla muy interactiva (tipo app) es más natural en React (Livewire acerca la distancia), PHP tiene "mala fama" por código viejo de otras épocas, y para tiempo real (chat, notificaciones al instante) Firebase es más directo.

### SQL (MySQL) contra NoSQL (Firebase, MongoDB)

| Tema | SQL (MySQL) | NoSQL (Firebase / MongoDB) |
|---|---|---|
| Forma de los datos | **Tablas** con columnas fijas | **Documentos** (tipo JSON) flexibles |
| Relaciones | **Claves foráneas y JOIN**: un CV apunta a su usuario y la base lo controla | Se duplican datos o se hacen varias consultas a mano |
| Integridad | La base **impide** errores: no deja un CV con un `user_id` que no existe; con `cascade` borra lo que depende | La app tiene que cuidar todo; la base acepta lo que le mandes |
| Reportes | `GROUP BY`, `COUNT`, `SUM`, `JOIN` en una sola consulta | Limitados; muchas veces hay que traer todo y calcular en el código |
| Transacciones | "Todo o nada": si falla un paso, se deshace todo (clave en pagos, stock, turnos) | Existen, pero son más limitadas |
| Cambios de estructura | Con migraciones (más orden, menos libertad) | Libre: agregás campos cuando quieras |
| Tiempo real y sin servidor | No es su fuerte | **Su gran ventaja** (Firebase) |
| Escalar a millones de usuarios | Se puede, con más trabajo | Más fácil de repartir en muchos servidores |

**Cuándo usar cada una:**
- **SQL** cuando los datos están **muy relacionados** y hay que **sacar reportes**: sistemas de gestión, turnos, stock, facturación, salud. Es el caso de este proyecto: usuarios → CV → experiencias, estudios y habilidades.
- **NoSQL** cuando los datos son **sueltos o cambian de forma**, o se necesita **tiempo real**: chats, métricas, apps móviles. Por eso en la app CV Belgrano Firebase se usa para el chat y las métricas, y los CV quedan en el celular.

Para la entrevista: *"Trabajé con NoSQL (Firebase) y ahora con SQL (MySQL). Elijo según los datos: si están muy relacionados y hacen falta reportes, SQL; si necesito tiempo real o estructura flexible, NoSQL."*

---

## 8. JOIN no es AJAX

Suenan parecido pero trabajan en **lugares distintos**.

| | **AJAX** | **JOIN** |
|---|---|---|
| Qué es | Un pedido del **navegador al servidor** sin recargar la página | Una operación de **SQL** que **une dos tablas** en una sola consulta |
| Dónde ocurre | Entre el navegador y Laravel (por internet, protocolo HTTP) | **Dentro de MySQL** |
| Cómo se escribe | En JavaScript: `fetch('/cvs')`, `axios.get(...)` | En SQL: `... JOIN cvs ON cvs.user_id = users.id` |
| Usa | Métodos HTTP: **GET** (traer), **POST** (crear), **PUT/PATCH** (editar), **DELETE** (borrar) | Columnas que se relacionan (clave foránea = clave primaria) |

### Qué es AJAX

**AJAX** = *Asynchronous JavaScript And XML* (JavaScript asíncrono y XML).

- **Antes de AJAX:** cada clic en un enlace o formulario **recargaba la página entera**. El servidor mandaba todo el HTML de nuevo.
- **Con AJAX:** JavaScript pide **solo los datos** al servidor, **en segundo plano** (asíncrono: la página no se congela mientras espera) y actualiza **una parte** de la pantalla.
- **La "X" de XML quedó vieja:** hoy los datos viajan casi siempre en **JSON**. El nombre quedó por costumbre.
- **Hoy se hace con** `fetch()` (nativo del navegador) o librerías como **axios**. Cuando en React hacés `fetch('/api/...')` dentro de un `useEffect`, **eso es AJAX**.

Ejemplo: el "me gusta" de una red social. Tocás el corazón, JavaScript manda un POST en segundo plano y solo cambia el ícono. La página no se recarga.

### El recorrido completo

```text
Navegador ──(AJAX / HTTP GET /cvs)──> Laravel ──(SQL con JOIN)──> MySQL
Navegador <──(HTML o JSON)────────── Laravel <──(filas)────────── MySQL
```

1. El usuario toca un botón. El navegador hace un pedido **HTTP** (con AJAX si es sin recargar).
2. Laravel recibe el pedido y le pregunta a MySQL con **SQL** (ahí puede haber un **JOIN**).
3. MySQL une las tablas y devuelve las filas.
4. Laravel arma la respuesta y la manda al navegador.

**Livewire usa AJAX por detrás:** cuando un componente se actualiza sin recargar la página, hace un pedido al servidor. No hace falta escribir `fetch` a mano.

### Ejemplo de JOIN

Tabla `users`:

| id | name |
|---|---|
| 1 | Leo |
| 2 | Ana |

Tabla `cvs`:

| id | user_id | title |
|---|---|---|
| 10 | 1 | CV desarrollador |
| 11 | 1 | CV soporte técnico |
| 12 | 2 | CV administrativa |

Consulta: "cada CV con el nombre de su dueño":

```sql
SELECT users.name, cvs.title
FROM cvs
JOIN users ON users.id = cvs.user_id;
```

Resultado:

| name | title |
|---|---|
| Leo | CV desarrollador |
| Leo | CV soporte técnico |
| Ana | CV administrativa |

El `JOIN ... ON users.id = cvs.user_id` le dice a MySQL: "para cada CV, buscá en `users` la fila cuyo `id` sea igual al `user_id` del CV, y pegalas". Por eso la **clave foránea** es tan importante: es el "pegamento" del JOIN.

En Firebase o MongoDB esto se hace con dos consultas y uniendo los datos en el código. En SQL es **una sola consulta** y la hace la base.

---

## 9. Caso práctico: vencimiento de un plan premium (Firebase contra Laravel + MySQL)

### Aclaración importante: la base de datos no "escucha" el reloj

**MySQL no avisa sola** cuando se cumple una fecha y **no le manda mensajes al celular**. Una base de datos **guarda y responde**. Siempre tiene que haber **alguien despierto que pregunte**:

| Quién pregunta | Ventajas | Contras |
|---|---|---|
| **La app del usuario** (lo que hace hoy la app con Firebase) | Gratis, sin servidor | Solo se entera cuando el usuario abre la app; el chequeo vive en el celular |
| **Un servidor con una tarea programada** (Laravel + MySQL, o Cloud Functions en Firebase) | Avisa aunque el usuario no abra la app; el control es central | Hay que tener un servidor encendido |

Con Firebase, las **funciones programadas** (Cloud Functions) piden el **plan pago** (Blaze). Con Laravel, el **hosting que ya pagás para la web** trae el **programador de tareas** sin costo extra.

### Truco de diseño que sirve en cualquier base

No hace falta "apagar" el premium cuando vence. Se guarda la fecha y se **calcula** en el momento:

```text
¿Es premium?  =  premium_hasta > ahora
```

Así nunca queda desactualizado, aunque nadie haya corrido nada. La tarea programada solo hace falta para **avisarle** al usuario.

### Las tablas

```text
users          subscriptions                              devices
id | name      id | user_id | plan    | ends_at    | notified_at   id | user_id | push_token
1  | Leo       1  | 1       | premium | 2026-12-01 | NULL          1  | 1       | abc...
2  | Ana       2  | 2       | premium | 2027-03-01 | NULL          2  | 2       | xyz...
```

`ends_at` lleva un **índice**, así MySQL encuentra los vencidos rápido sin revisar todas las filas.

### La consulta: los vencidos y a quién avisarles, en un solo pedido

```sql
SELECT users.name, devices.push_token
FROM subscriptions
JOIN users   ON users.id = subscriptions.user_id
JOIN devices ON devices.user_id = users.id
WHERE subscriptions.ends_at < NOW()
  AND subscriptions.notified_at IS NULL;
```

Y marcar a **todos** como avisados en **una sola instrucción**, sin `forEach`:

```sql
UPDATE subscriptions
SET notified_at = NOW()
WHERE ends_at < NOW() AND notified_at IS NULL;
```

### En Laravel

```php
// routes/console.php: el "reloj". Laravel corre esta tarea todos los días a las 9
Schedule::command('subscriptions:notify-expired')->dailyAt('09:00');
```

```php
// La tarea (un comando de Artisan)
$vencidas = Subscription::with('user.devices')      // relaciones = los JOIN
    ->where('ends_at', '<', now())
    ->whereNull('notified_at')
    ->get();

foreach ($vencidas as $sub) {
    $sub->user->notify(new PremiumVencido());       // push (FCM) o mail, en una cola
}

Subscription::whereIn('id', $vencidas->pluck('id'))->update(['notified_at' => now()]);
```

En el hosting se configura **una sola vez** un *cron* (el reloj del servidor) que corre `php artisan schedule:run` cada minuto. Laravel decide qué tareas tocan.

Sigue habiendo un `foreach`, porque cada usuario recibe **su propio** mensaje. La diferencia está en el trabajo pesado: **buscar, filtrar y cruzar** lo hace la base, con índices, en una consulta. No se trae todo a JavaScript para filtrar a mano.

### Entonces, ¿por qué elegir SQL acá?

| Tema | Firebase (como en la app) | Laravel + MySQL |
|---|---|---|
| Buscar vencidos | Consulta + filtro, o lógica en la app | `WHERE ends_at < NOW()` con índice |
| Cruzar con usuario y dispositivo | Varias lecturas y unir en el código | **Un JOIN** |
| Marcar a todos | Una escritura por documento | **Un UPDATE** para todos |
| Avisar sin que el usuario abra la app | Cloud Functions (plan pago) | Programador de tareas (incluido en el hosting) |
| Escuchar cambios en vivo en la app (`onSnapshot`) | **Su punto fuerte** | No lo hace solo (hace falta WebSockets, por ejemplo Laravel Reverb) |

**Conclusión:** no es "lo mismo con otra técnica". Si los datos están **relacionados** y hay que **filtrar y cruzar muchos registros**, SQL lo hace en la base, en pocas líneas y con garantías. Si hace falta **tiempo real** o **no tener servidor**, Firebase es mejor. Para la app CV Belgrano, que funciona sin servidor, Firebase fue la elección correcta. Para un sistema de gestión con usuarios, planes y reportes, Laravel + MySQL.

### ¿Quién filtra? En el celular o en la base

**Filtrar en el celular:** la app trae los datos y los recorre con `forEach` / `filter` en JavaScript.

**Filtrar en la base:** se le manda a MySQL una consulta que dice **qué** se quiere (`WHERE ends_at < NOW()`), no **cómo** buscarlo. El **motor de MySQL** (el programa del servidor, no un código guardado en la base) decide la forma más rápida, usando índices, y devuelve **solo el resultado**.

| Ventaja de filtrar en la base | Por qué |
|---|---|
| **Viajan menos datos** | De 10.000 filas llegan solo las 3 vencidas. Menos internet, menos batería |
| **Más rápido** | Con un índice, MySQL va directo a las filas que sirven, como el índice de un libro |
| **Más seguro** | El chequeo está en un solo lugar central. En el celular alguien podría modificar la app |
| **Igual para todos** | La regla está en un lugar; no depende de la versión de la app que tenga cada usuario |
| **Más barato en servicios que cobran por lectura** | Firestore cobra por cada documento leído. Traer todo para filtrar en el teléfono = pagar todas esas lecturas |

**Para ser justos con Firebase:** Firestore también filtra en su servidor con `where()` e índices. Lo que **no** hace es **unir colecciones (JOIN)** ni reportes complejos en una sola consulta. Por eso, con Firebase, muchas veces esa parte termina en el código de la app.

**Aclaración:** MySQL permite guardar código dentro de la base (*procedimientos almacenados*, *triggers*, *eventos*), pero en Laravel casi no se usa: la lógica va en PHP y a la base se le mandan consultas.

### El índice es el "cuaderno con señaladores"

Un **índice** es como dejar señaladores en un cuaderno: para encontrar las hojas marcadas no hace falta recorrer todas. **Firestore también tiene índices**: una consulta con `where('vence', '<', ahora)` desde un servidor Node lee **solo** los documentos vencidos, no todos. "Leer todo" pasa cuando se filtra **en el código**, no por usar Firebase.

Lo que Firestore **no** tiene es:
- **JOIN**: unir colecciones en una consulta (vencidos + su usuario + su celular).
- **Reportes libres**: `GROUP BY`, sumas y promedios combinando colecciones.
- **Transacciones grandes**: muchas filas de muchas tablas, todo o nada.

Esa es la ventaja real de SQL cuando **los dos** tienen servidor.

### Reglas en el servidor: "igual para todos" y "más seguro"

**Igual para todos.** Si la regla "el premium funciona 30 días sin conexión" está escrita **en la app**, y después se cambia a 15 días, quien no actualice la app sigue con 30. Si la regla está **en el servidor**, se cambia una vez y vale para todos al instante.

**Más seguro.** La seguridad **no depende de la marca** (Laravel o Firebase) sino de **dónde se toma la decisión**:
- Si la decide **el dispositivo** (celular o navegador), se puede modificar: un APK se puede desarmar y el JavaScript de una web se puede cambiar desde el navegador.
- Si la decide **el servidor**, modificar el dispositivo no sirve de nada.

### Comparación justa: la misma web, con internet, hecha de dos formas

Caso: "Ana no puede ver ni editar el CV de Leo, y solo un premium descarga el diseño premium".

| | **A. React + Firestore directo** (el navegador habla con la base) | **B. Laravel + MySQL** (el navegador habla con Laravel, Laravel con la base) |
|---|---|---|
| ¿Quién puede tocar la base? | **El navegador de cualquiera**, con el SDK de Firebase | **Solo el servidor**. La contraseña de MySQL nunca sale de ahí |
| ¿Dónde está la protección? | En las **Security Rules**, un lenguaje aparte y limitado | En **tu código PHP**: Policy, Form Request, controlador |
| Si te olvidás una regla | **Los datos quedan expuestos**: cualquiera puede leer o escribir desde la consola del navegador | El dato no sale si ningún controlador lo devuelve |
| Validar datos complejos | Difícil en las reglas | Form Requests con todas las reglas de PHP |
| PDF premium | Si se arma **en el navegador**, se puede saltear el chequeo | Se arma **en el servidor** después de la Policy |
| Protecciones incluidas | Las que escribas en las reglas | CSRF, contraseñas cifradas, protección contra inyección SQL |

**Pero:** si en A se agregan **Cloud Functions** para todo lo importante (validar, decidir premium, armar el PDF), queda **igual de seguro** que B.

**Conclusión honesta:**
- Laravel **no es más seguro por ser Laravel**.
- La diferencia es el **camino por defecto**. En Laravel, todo pedido **pasa por tu código en el servidor**: lo seguro es lo normal. En Firebase, el camino por defecto es **el cliente hablando directo con la base**, y la seguridad depende de escribir bien las reglas y de mover lo importante a Cloud Functions.
- Firebase **sin servidor** y una app **offline** no se pueden comparar con una web con servidor: son situaciones distintas.

**En este proyecto (Laravel):** la `CvPolicy` decide en el servidor quién puede ver cada CV y el PDF se arma en el servidor. Aunque alguien modifique lo que manda su navegador, el servidor vuelve a chequear.

### Extra: MySQL sí puede hacer tareas programadas sola, pero limitadas

MySQL tiene **eventos** (`CREATE EVENT ... ON SCHEDULE EVERY 1 DAY DO UPDATE ...`), que corren SQL solos a una hora. Sirven para **actualizar datos**, pero **no pueden mandar una notificación ni un mail**. Para eso siempre hace falta el código del servidor (Laravel).

---

## 10. ¿Para qué login y base de datos en esta web?

Pregunta válida: la app CV Belgrano **no tiene login**, y para **mostrar** un CV alcanza con HTML.

**Cuándo NO hace falta servidor:** si la web solo deja **armar un CV y bajarlo en el momento**, puede guardar todo en el navegador (`localStorage`) y armar el PDF ahí mismo. Es la misma filosofía que la app: sin cuenta, los datos en el dispositivo.

**Cuándo SÍ tiene sentido login + base:**

| Necesidad | Sin login | Con login |
|---|---|---|
| Empezar el CV en la PC y seguirlo en otra PC | ❌ Los datos quedan en ese navegador | ✅ |
| Que no se pierda si se borra el navegador | ❌ | ✅ |
| Tener varios CV (uno por puesto) guardados | Solo en ese navegador | ✅ En cualquier lugar |
| Diseños premium protegidos (PDF armado en el servidor) | ❌ El navegador se puede modificar | ✅ El servidor decide |
| A futuro: sincronizar con la app del celular | ❌ | ✅ Con una API (Sanctum) |
| Saber cuántos CV se crean y qué habilidades se repiten | ❌ | ✅ Con consultas SQL |

**El costo de guardar datos:** un CV tiene datos personales (nombre, teléfono, email). Guardarlos en un servidor es una **responsabilidad**: en Argentina rige la Ley 25.326 de Protección de Datos Personales. Por eso la app eligió no guardar nada.

**Por qué este proyecto lleva login:**
1. **Para la postulación:** el puesto es de **sistemas de gestión** (usuarios, permisos, registros, reportes). Una página de HTML no muestra PHP, MySQL, JOIN, índices ni Policies. Este es el motivo principal.
2. **Para el producto:** una **versión para PC** con CV guardados y, más adelante, diseños premium protegidos y sincronización con la app.

**Mejora posible (próximos pasos):** un modo **"probar sin cuenta"** que guarde en el navegador, con la cuenta como **opcional** para guardar y sincronizar. Lo mejor de las dos formas.

---

## 11. Relaciones de Eloquent

### La idea

La **tabla** ya sabe que un CV pertenece a un usuario: tiene la clave foránea `user_id`. Pero el **código PHP** todavía no lo sabe. Las relaciones se escriben **en los modelos** para que Laravel pueda navegar de uno a otro sin escribir SQL.

```text
User  ──tiene muchos (hasMany)──>  Cv
Cv    ──pertenece a (belongsTo)──> User
```

Una relación se declara **de los dos lados**, porque se puede navegar en las dos direcciones.

### Cómo se escriben

En `app/Models/User.php`:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function cvs(): HasMany
{
    return $this->hasMany(Cv::class);
}
```

En `app/Models/Cv.php`:

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

| Parte | Significado |
|---|---|
| `public function cvs()` | El **nombre** con que vas a pedir la relación: `$user->cvs`. En plural porque son muchos |
| `public function user()` | En singular porque es uno solo |
| `: HasMany` / `: BelongsTo` | El **tipo de retorno** (opcional, pero ayuda al editor y a quien lee) |
| `Cv::class` | "La clase Cv". Es el nombre completo del modelo como texto (`App\Models\Cv`) |

**¿Cómo sabe Laravel qué columna usar?** Por **convención**: para `belongsTo(User::class)` busca la columna `user_id` (nombre del modelo en minúscula + `_id`). Por eso la columna se llamó así. Si se llamara distinto, habría que decírselo: `belongsTo(User::class, 'dueno_id')`.

### Cómo se usan y qué SQL hace Laravel

| Código | SQL que corre por detrás |
|---|---|
| `$user->cvs` | `SELECT * FROM cvs WHERE user_id = 1` |
| `$cv->user` | `SELECT * FROM users WHERE id = 1 LIMIT 1` |
| `$user->cvs()->create([...])` | `INSERT INTO cvs (user_id, ...) VALUES (1, ...)` y completa `user_id` solo |
| `$user->cvs()->count()` | `SELECT COUNT(*) FROM cvs WHERE user_id = 1` |

**`$user->cvs` contra `$user->cvs()`:**
- **Sin paréntesis** (propiedad): trae los resultados, una colección de CV.
- **Con paréntesis** (método): devuelve la **consulta sin ejecutar**, para seguir agregándole cosas: `$user->cvs()->where('template', 'ats')->get()`.

### "Por convención", explicado

**Convención** = una regla de nombres que Laravel da por sentada, para no tener que configurarla.

Cuando escribís `$this->belongsTo(User::class)` dentro de `Cv`, Laravel razona así:
1. "Me dicen que el CV pertenece a un **User**."
2. "Paso el nombre a minúscula y le agrego `_id`: **`user_id`**."
3. "Busco en la tabla `cvs` la columna `user_id` y con ese número busco en `users` el `id` igual."

No hay magia: es una regla de nombres. Otras convenciones que ya usaste:
- Modelo `Cv` → tabla `cvs` (plural).
- Toda tabla tiene su clave primaria en `id`.

Si se rompe la regla (por ejemplo, la columna se llama `dueno_id`), hay que decírselo a mano: `belongsTo(User::class, 'dueno_id')`.

### El problema N+1 y `with()`

**Analogía:** tenés que llevar 50 cartas a 50 casas. Opción mala: vas a la oficina, buscás una carta, la llevás, volvés, buscás otra... **50 viajes**. Opción buena: buscás **las 50 juntas** en un solo viaje y después las repartís. Cada "viaje" es **una consulta a MySQL**, y cada viaje tarda.

Mostrar una lista de 50 CV con el nombre de su dueño:

```php
$cvs = Cv::all();                 // 1 consulta
foreach ($cvs as $cv) {
    echo $cv->user->name;         // 1 consulta POR CADA CV → 50 más
}
// Total: 51 consultas (1 + N). Lento.
```

Con **carga anticipada** (*eager loading*):

```php
$cvs = Cv::with('user')->get();   // 2 consultas en total
// SELECT * FROM cvs
// SELECT * FROM users WHERE id IN (1, 2, 5, ...)
```

Es una pregunta típica de entrevista: *"¿Qué es el problema N+1 y cómo lo evitás?"* → *"Pasa cuando se carga una relación dentro de un bucle; se evita con `with()`."*

**¿Y el JOIN?** `with()` hace **dos consultas** separadas, no un JOIN. Para reportes (contar, agrupar), se usa un JOIN explícito con el Query Builder (sección 8). Las dos formas son válidas; cada una para su caso.

### `$fillable`: qué campos se pueden llenar de una vez

```php
protected $fillable = ['title', 'full_name', 'headline', 'email', 'phone', 'location', 'summary', 'template'];
```

Cuando se crea un registro con un arreglo (`Cv::create($datos)`), Laravel **solo acepta los campos de esta lista**. Es una protección: si alguien agrega a escondidas `user_id=5` en un formulario, se ignora. Por eso **`user_id` no va en la lista**: se completa por la relación (`$user->cvs()->create(...)`).

### Probar sin pantallas: Tinker

```powershell
php artisan tinker
```

Abre una **consola interactiva de PHP con todo el proyecto cargado**. Sirve para probar modelos y relaciones antes de tener pantallas:

```php
$user = App\Models\User::first();          // el primer usuario
$user->cvs()->create(['title' => 'CV prueba', 'full_name' => 'Leo', 'email' => 'leo@test.com']);
$user->cvs;                                 // lista sus CV
App\Models\Cv::first()->user->name;         // del CV a su dueño
exit                                        // salir
```

Lo que se crea en Tinker **se guarda de verdad** en MySQL.

---

## 12. Glosario

| Palabra | Significado |
|---|---|
| **Framework** | Estructura ya armada con reglas y herramientas para no empezar de cero |
| **MVC** | Modelo (datos), Vista (lo que se ve), Controlador (la lógica que une los dos) |
| **Eloquent** | El sistema de Laravel para usar tablas como objetos PHP (ORM) |
| **Migración** | Archivo PHP que crea o modifica una tabla |
| **Factory** | Generador de datos de prueba |
| **Seeder** | Carga datos iniciales o de ejemplo en la base |
| **Clave primaria (PK)** | La columna que identifica cada fila (`id`) |
| **Clave foránea (FK)** | Columna que apunta a la fila de otra tabla (`user_id` → `users.id`) |
| **Índice** | "Índice de libro" de una columna: hace más rápidas las búsquedas por ella |
| **Servicio de Windows** | Programa que corre en segundo plano y arranca con la PC (así corre MySQL) |
| **Puerto** | "Número de puerta" por donde un servidor escucha (MySQL: 3306) |
| **Local / producción** | Tu PC (para desarrollar) / el servidor online (lo que usa la gente) |
