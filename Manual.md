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

### ¿Y el archivo `database/database.sqlite`?

**SQLite** es una base de datos que vive **en un solo archivo**, sin servidor. Es útil para pruebas, apps de celular o de escritorio.

- **Por qué está:** el instalador de Laravel arranca por defecto con SQLite y creó ese archivo. **No elegiste mal:** después lo cambiamos a MySQL en el `.env`.
- **Cómo saber cuál se usa:** lo decide la línea `DB_CONNECTION` del `.env`. Hoy dice `mysql`. El comando `php artisan db:show` lo confirma: muestra `MySQL 26.10` y la base `cv_belgrano_web`.
- **¿Se sube a GitHub?** No. El archivo `database/.gitignore` dice `*.sqlite*`, así que Git lo ignora.
- **¿Se puede borrar?** Sí, no se usa. También puede quedar ahí sin molestar.
- **Dónde sí se usa SQLite:** en las **pruebas automáticas** (más adelante). Se crea una base en memoria que desaparece al terminar, para que las pruebas sean rápidas y no ensucien tu MySQL.

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

## 7. Glosario

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
