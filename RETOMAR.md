# RETOMAR: CV Belgrano Web

> **Para la IA que me ayuda a estudiar:** leé este archivo entero antes de responder. Acá está quién soy, dónde quedé, qué toca hoy y qué reglas seguir.
> Última actualización: **18/11/2026 (cierre)** (la actualiza Claude cuando Leo le cuenta el avance).

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
- Aprendo Laravel desde cero con este proyecto para postularme a **Desarrollador/a Web Junior PHP/Laravel, remoto, en Nodo Austral Sistemas** (sistemas de gestión para pymes de salud). Usan **Blade + Livewire**. El aviso cierra el **02/01/2027**, pero **la meta es postularme alrededor del 01/12/2026**.
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

## 3. Dónde estoy (18/11/2026, cierre del día)

**Hecho hasta el 17/11:** entorno, Laravel 13 + Livewire, MySQL, registro y login. Tablas `cvs`, `experiences`, `education` y `skills` con relaciones, factories y seeder (Bloque 1).

**Hecho el 18/11** (explicado con ejemplos en `registro/2026-11-18.md`):
- **Sin Flux ni Tailwind** (desinstalados). Login, registro, logout y recuperar contraseña quedaron en HTML común en `resources/views/auth/`. Se borraron los ajustes del kit y **Mis perfiles**.
- **Un solo molde:** `layouts/app.blade.php` (`<x-layouts::app>`), con `$slot`, `$head`, `$title` y `:menu="false"` para la home.
- **Home = la landing de CV Belgrano** pasada de React a componentes Blade (`components/landing/*`), con las imágenes en `public/images/landing` y los textos en **`lang/es`, `lang/en` y `lang/pt`** (`landing.php`). Componente nuevo `probar-online`, que lleva a `/crear`, y botón `volver-arriba` (aparece al bajar, solo con CSS).
- **Selector de idioma:** componente Livewire `livewire/idioma.blade.php` (estado `$abierto`; `cambiar()` guarda en la sesión). Middleware `ElegirIdioma` (en `bootstrap/app.php`): primero el idioma elegido, después el del navegador.
- **`/crear` (público):** cuatro componentes Livewire en `livewire/creador/` (datos, experiencia, educación, habilidades). Cada uno tiene modal propio en Livewire puro (`$abierto` + `@if`), borrador, validación con mensajes en español y `#[Session]`. **Sin base de datos todavía.** Después del login se va a `/crear`.
- **Un CSS por componente** (`resources/css/...`, todos en el `input` de `vite.config.js`), con los colores de la landing.
- **Sesión en archivos:** `SESSION_DRIVER=file`. `APP_LOCALE=es`.
- **Convenciones:** componentes Livewire **sin ⚡**, en `resources/views/livewire/`. Los datos fijos (idiomas, URLs) se escriben en el archivo que los usa, sin `config/` propio.
- Pruebas: 13 en verde.

**Pendiente:**
- El logo de CV Belgrano en `/crear` (lo hace Leo).
- Ejercicios del final de `registro/2026-11-18.md`.
- `/crear` y las pantallas de cuenta siguen solo en español.
- Vista previa ATS que reciba los datos de las secciones (props), después PDF.
- Conectar con la base: "Guardar" con login pasa lo de la sesión a `cvs` y sus tablas hijas, con `CvPolicy`.

**▶ RETOMAR ACÁ:** repasar `registro/2026-11-18.md`, poner el logo en `/crear` y después la **vista previa ATS** en `/crear`.

---

## 4. Plan por bloques (por la mañana; la tarde va para la app de CV)

Ritmo decidido el 16/11: **mañanas para Laravel (3 a 4 h), tardes para la app**. Un bloque lleva 1 o 2 mañanas, así que las fechas son aproximadas. Cada bloque termina con algo que funciona y **un commit**. Si un bloque se atrasa, se recorta (✂️), no se estira.

**Postulo cuando estén hechos los bloques 1 a 7** (CRUD con Policy, PDF, pruebas, JOIN y README). La landing y el deploy van **después de postular**, y el link se suma al README.

| # | Mañanas (aprox.) | Bloque | Conceptos a aprender | Termina cuando… |
|---|---|---|---|---|
| 1 | 17-18/11 | **Datos de prueba + tablas hijas.** `CvFactory` con `fake()`. `DatabaseSeeder` con un usuario demo (`demo@cvbelgrano.test`) y 2 perfiles. Migraciones `experiences`, `education` y `skills` (FK a `cvs` con cascada, `sort_order`, **índice en `skills.name`**), sus modelos, factories y relaciones `hasMany` en `Cv` | Factory, seeder, `fake()`, `has()`/`for()`, índices, cascada | `php artisan migrate:fresh --seed` crea todo sin errores y en Tinker `Cv::first()->experiences` trae datos |
| 2 | 19-20/11 | **Pantalla "Mis perfiles".** Ruta protegida con `auth`, componente Livewire que lista los perfiles del usuario, enlace en el menú del starter kit. Ver el **N+1** y arreglarlo con `with()`/`withCount()` | `routes/web.php`, middleware, componente Livewire (clase + vista Blade), layout del kit, Flux | Logueado, veo mis perfiles; sin login me manda al login |
| 3 | 21-22/11 | **Creador público + guardar con login** (decisión del 17/11, ver §3). **Crear, editar y borrar perfil.** Formulario Livewire con **validación** y mensajes en español. **`CvPolicy`**: nadie ve ni toca perfiles ajenos (403) | `wire:model`, validación, Policy, `authorize` | Creo, edito y borro desde el navegador; con otro usuario recibo 403 |
| 4 | 23-24/11 | **Secciones del perfil.** Agregar y borrar experiencias, estudios y habilidades dentro del editor (✂️ editar y reordenar con `sort_order`, si sobra tiempo) | Relaciones en formularios, componentes o arreglos en Livewire | El perfil demo se carga completo desde la web |
| 5 | 25-26/11 | **Plantilla ATS + PDF.** Vista Blade `ats` (sin flexbox, porque dompdf no lo soporta bien), vista previa y botón "Descargar PDF" con `barryvdh/laravel-dompdf` | Vistas Blade, `@foreach`, `@if`, instalar un paquete con Composer, respuestas de descarga | Baja un PDF prolijo con los datos del perfil |
| 6 | 27-28/11 | **Pruebas + calidad.** Pest: (1) sin login → login, (2) crear perfil guarda en la base, (3) la validación falla sin nombre, (4) **otro usuario recibe 403**, (5) borrar un perfil borra sus secciones, (6) el PDF responde `application/pdf`. Pint. GitHub Actions que corre Pint y los tests | Pest, `actingAs`, `assertDatabaseHas`, `assertForbidden`, CI | `php artisan test` en verde e insignia verde en GitHub |
| 7 | 29-30/11 | **JOIN + README.** Página "Estadísticas" con **Query Builder**: las 10 habilidades más usadas (`skills` JOIN `cvs`, `groupBy`, `count`) + su prueba. README con capturas o GIF, diagrama Mermaid de las tablas, instalación, usuario demo y decisiones | JOIN escrito a mano, `groupBy`, Eloquent contra Query Builder | Se ve el ranking y el README se entiende solo |
| 8 | 01/12 | **Postulación.** Actualizar CV y LinkedIn solo con lo hecho (archivos 04 y 05 del plan), post en LinkedIn, postular en Qontrata. Practicar en voz alta cómo explicar el proyecto | Contar decisiones: perfiles, cascada, Policy, N+1, índices, JOIN | **Postulación enviada** |
| 9 | después | **Landing + deploy.** Landing `/` en Blade (qué es CV Belgrano y "Crear mi CV gratis") y subir a un hosting barato que no se duerma. Sumar el link al README y a LinkedIn | Deploy, `.env` de producción, `APP_URL` real | Hay un link público |

Se trabaja todos los días, sin días libres.

✂️ **Orden de recorte si me atraso:** reordenar secciones → editar filas (dejar agregar y borrar) → GitHub Actions. **No se recorta:** CRUD con Policy, JOIN, pruebas, PDF y README.

---

## 5. Registro de avance

| Fecha | Qué hice |
|---|---|
| 14/11 | Plan y estrategia de la postulación |
| 15/11 | Entorno, Laravel + Livewire, MySQL, tabla `cvs`, `Manual.md` |
| 16/11 | Relaciones User ↔ Cv, Tinker, `$fillable`, `comandos.tinker.md`. Entendí Artisan, `APP_URL` y `migrate` |
| 17/11 | Bloque 1 completo (tablas hijas, factories, seeder). Bloque 2 a medias (páginas, molde, Vite). Tinker y `with()`. Decisión: login solo para guardar. Ver `registro/2026-11-17.md` |
| 18/11 | Sin Flux ni Tailwind. Home = landing (es/en/pt, selector Livewire + middleware, volver arriba). `/crear` con 4 modales Livewire y `#[Session]`. CSS por componente. Ver `registro/2026-11-18.md` |

---

## 6. Cómo se actualiza este archivo

Al terminar el día le cuento a Claude qué hice ("terminé el bloque N", "me trabé en X"). Claude revisa el repo, actualiza las secciones 3 a 5 y hace el commit. La IA de estudio no edita este archivo: sigue lo que dice.
