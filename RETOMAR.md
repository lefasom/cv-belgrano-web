# RETOMAR: CV Belgrano Web

> **Para la IA que me ayuda a estudiar:** leé este archivo entero antes de responder. Acá está quién soy, dónde quedé, qué toca hoy y qué reglas seguir.
> Última actualización: **16/11/2026** (la actualiza Claude cuando Leo le cuenta el avance).

---

## 0. Reglas para la IA

1. **Yo (Leo) escribo todo el código y hago los commits.** Vos explicás, me guiás y revisás lo que escribí. No me des archivos completos para copiar: decime qué va, dónde y por qué.
2. **Primero el concepto, después el código.** Antes de algo nuevo, explicámelo simple (analogía o tabla), comparando con lo que ya sé: JavaScript, React/React Native, Firebase.
3. **Todo en español.** Respuestas cortas y por pasos.
4. **Commits sin coautor de IA** (sin `Co-Authored-By`). El repo tiene que ser 100 % mío.
5. **Versiones reales del proyecto:** Laravel 13, Livewire 4 + Flux (starter kit oficial), Pest, PHP 8.5, MySQL 26.10. Si no estás seguro de que algo existe en estas versiones, decilo y lo verificamos con `php artisan ...` o la documentación oficial. Ojo: en Laravel 13 el modelo `User` usa atributos como `#[Fillable([...])]`; en `Cv` uso `protected $fillable`. Las dos formas valen.
6. **Antes de un comando que borra datos** (`migrate:fresh`, `migrate:refresh`, `migrate:rollback`, `truncate`), avisame qué se pierde.
7. **No dejes que el autocompletado meta código que no entiendo.** Si VS Code sugiere algo, lo revisamos antes.
8. **Al terminar cada bloque:** dame un resumen corto para mi `Manual.md` y el mensaje del commit.

### Qué SÍ usar (lo que pide el aviso)
PHP orientado a objetos · Laravel (MVC, rutas, middleware, Eloquent, **Query Builder con JOIN**) · MySQL (diseño de tablas, claves foráneas, **índices**) · **Blade + Livewire** + algo de JavaScript · **pruebas** (Pest) · Pint · **Git** diario · documentar.

### Qué NO usar (fuera del aviso; no perder tiempo)
React, Vue o Inertia en esta web · Filament o paneles generados · API REST, Sanctum o JWT · Docker o Sail · Firebase o MongoDB · pagos, premium o anuncios · colas, websockets o microservicios · Xdebug y Gherkin solo como cultura general · diseño fino de CSS (usar Flux y Tailwind como vienen).

---

## 1. Quién soy y para qué es esto

- Leo, desarrollador de **React Native** (app CV Belgrano) y de **FULLMED** (sistema de brazaletes en 8 hospitales). UTN hasta 3.er año.
- Aprendo Laravel desde cero con este proyecto para postularme a **Desarrollador/a Web Junior PHP/Laravel, remoto, en Nodo Austral Sistemas** (sistemas de gestión para pymes de salud). Usan **Blade + Livewire**. El aviso cierra el **02/01/2027**, pero **la meta es postularme alrededor del 03/12/2026**.
- **CV Belgrano Web** es la versión web de mi app: login, varios perfiles de CV por usuario, una plantilla ATS y PDF. Tiene que valer sola como vidriera de la app: solo plantillas gratis, sin premium.
- Proyecto: `C:\Users\leo\Herd\cv-belgrano-web` · Repo público: https://github.com/lefasom/cv-belgrano-web
- Documentos de apoyo: `Manual.md` (lo que aprendí) y `comandos.tinker.md` (chuleta de Tinker y Artisan). El plan original está en `C:\Users\leo\Desktop\App-de-cv\CV_BELGRANO_WEB_LARAVEL\`.

---

## 2. Encender y apagar

**Al empezar:**
1. `Get-Service MySQL2610` → tiene que decir `Running` (arranca solo con la PC).
2. En VS Code, una terminal: `php artisan serve` → http://localhost:8000
3. Otra terminal: `npm run dev` (Vite: CSS y JS).
4. `php artisan migrate:status` → todo en `Ran`.

**Al terminar:** Ctrl + C en las dos terminales, y `exit` si Tinker quedó abierto. MySQL puede quedar encendido.

El `.env` tiene `APP_URL=http://localhost:8000` porque trabajo con `php artisan serve`, no con Herd. Si cambio de forma, cambio esa línea.

---

## 3. Dónde estoy (16/11/2026)

**Hecho:**
- Entorno, Laravel 13 + Livewire, MySQL conectado, registro y login.
- Tabla `cvs` (un "CV" = **un perfil de datos**; un usuario tiene muchos; el diseño vive en el código), con `user_id` → `users` en cascada.
- Relaciones escritas por mí: `Cv::user()` (`belongsTo`) y `User::cvs()` (`hasMany`). `$fillable` sin `user_id`: se crea con `$user->cvs()->create([...])`.
- Probado en Tinker. Hay 1 usuario y 1 CV de prueba.
- Entiendo: Artisan, Composer contra npm, `migrate` (pasa la estructura, no los datos), `up()`/`down()`, `APP_URL`, Herd contra `serve`.

**Lo próximo:** el Bloque 1.

---

## 4. Plan por bloques (por la mañana; la tarde va para la app de CV)

Ritmo decidido el 16/11: **mañanas para Laravel (3 a 4 h), tardes para la app**. Un bloque lleva 1 o 2 mañanas, así que las fechas son aproximadas. Cada bloque termina con algo que funciona y **un commit**. Si un bloque se atrasa, se recorta (✂️), no se estira.

**Postulo cuando estén hechos los bloques 1 a 7** (CRUD con Policy, PDF, pruebas, JOIN y README). La landing y el deploy van **después de postular**, y el link se suma al README.

| # | Mañanas (aprox.) | Bloque | Conceptos a aprender | Termina cuando… |
|---|---|---|---|---|
| 1 | 17-18/11 | **Datos de prueba + tablas hijas.** `CvFactory` con `fake()`. `DatabaseSeeder` con un usuario demo (`demo@cvbelgrano.test`) y 2 perfiles. Migraciones `experiences`, `education` y `skills` (FK a `cvs` con cascada, `sort_order`, **índice en `skills.name`**), sus modelos, factories y relaciones `hasMany` en `Cv` | Factory, seeder, `fake()`, `has()`/`for()`, índices, cascada | `php artisan migrate:fresh --seed` crea todo sin errores y en Tinker `Cv::first()->experiences` trae datos |
| 2 | 19-20/11 | **Pantalla "Mis perfiles".** Ruta protegida con `auth`, componente Livewire que lista los perfiles del usuario, enlace en el menú del starter kit. Ver el **N+1** y arreglarlo con `with()`/`withCount()` | `routes/web.php`, middleware, componente Livewire (clase + vista Blade), layout del kit, Flux | Logueado, veo mis perfiles; sin login me manda al login |
| 3 | 21-22/11 | **Crear, editar y borrar perfil.** Formulario Livewire con **validación** y mensajes en español. **`CvPolicy`**: nadie ve ni toca perfiles ajenos (403) | `wire:model`, validación, Policy, `authorize` | Creo, edito y borro desde el navegador; con otro usuario recibo 403 |
| 4 | 24-25/11 | **Secciones del perfil.** Agregar y borrar experiencias, estudios y habilidades dentro del editor (✂️ editar y reordenar con `sort_order`, si sobra tiempo) | Relaciones en formularios, componentes o arreglos en Livewire | El perfil demo se carga completo desde la web |
| 5 | 26-27/11 | **Plantilla ATS + PDF.** Vista Blade `ats` (sin flexbox, porque dompdf no lo soporta bien), vista previa y botón "Descargar PDF" con `barryvdh/laravel-dompdf` | Vistas Blade, `@foreach`, `@if`, instalar un paquete con Composer, respuestas de descarga | Baja un PDF prolijo con los datos del perfil |
| 6 | 28-29/11 | **Pruebas + calidad.** Pest: (1) sin login → login, (2) crear perfil guarda en la base, (3) la validación falla sin nombre, (4) **otro usuario recibe 403**, (5) borrar un perfil borra sus secciones, (6) el PDF responde `application/pdf`. Pint. GitHub Actions que corre Pint y los tests | Pest, `actingAs`, `assertDatabaseHas`, `assertForbidden`, CI | `php artisan test` en verde e insignia verde en GitHub |
| 7 | 01-02/12 | **JOIN + README.** Página "Estadísticas" con **Query Builder**: las 10 habilidades más usadas (`skills` JOIN `cvs`, `groupBy`, `count`) + su prueba. README con capturas o GIF, diagrama Mermaid de las tablas, instalación, usuario demo y decisiones | JOIN escrito a mano, `groupBy`, Eloquent contra Query Builder | Se ve el ranking y el README se entiende solo |
| 8 | 03/12 | **Postulación.** Actualizar CV y LinkedIn solo con lo hecho (archivos 04 y 05 del plan), post en LinkedIn, postular en Qontrata. Practicar en voz alta cómo explicar el proyecto | Contar decisiones: perfiles, cascada, Policy, N+1, índices, JOIN | **Postulación enviada** |
| 9 | después | **Landing + deploy.** Landing `/` en Blade (qué es CV Belgrano y "Crear mi CV gratis") y subir a un hosting barato que no se duerma. Sumar el link al README y a LinkedIn | Deploy, `.env` de producción, `APP_URL` real | Hay un link público |

Domingos libres (23/11 y 30/11) o de colchón.

✂️ **Orden de recorte si me atraso:** reordenar secciones → editar filas (dejar agregar y borrar) → GitHub Actions. **No se recorta:** CRUD con Policy, JOIN, pruebas, PDF y README.

---

## 5. Registro de avance

| Fecha | Qué hice |
|---|---|
| 14/11 | Plan y estrategia de la postulación |
| 15/11 | Entorno, Laravel + Livewire, MySQL, tabla `cvs`, `Manual.md` |
| 16/11 | Relaciones User ↔ Cv, Tinker, `$fillable`, `comandos.tinker.md`. Entendí Artisan, `APP_URL` y `migrate` |

---

## 6. Cómo se actualiza este archivo

Al terminar el día le cuento a Claude qué hice ("terminé el bloque N", "me trabé en X"). Claude revisa el repo, actualiza las secciones 3 a 5 y hace el commit. La IA de estudio no edita este archivo: sigue lo que dice.
