# 01_ERROR_422_AL_SUBIR_LA_FOTO

**Fecha:** 20/11/2026 · **Estado:** cerrada, verificada con `php artisan serve`

---

## 1. Síntoma

En `/crear` → Herramientas → **Agregar foto**, al elegir una imagen aparecía debajo del botón:

```
The archivo failed to upload.
```

Y en la consola del navegador:

```
Failed to load resource: the server responded with a status of 422 (Unprocessable Content)
```

---

## 2. Evidencia

La respuesta de la subida (`POST /livewire-…/upload-file`) tenía, antes del JSON, una advertencia de PHP:

```
Warning: PHP Request Startup: File upload error - unable to create a temporary file in Unknown on line 0
{"message":"The files.0 failed to upload.", …}
```

- El archivo pesaba 520 KB, así que no era el límite de PHP (`upload_max_filesize = 2M`).
- El mismo código funcionaba con un servidor PHP que tenía la variable `TEMP` definida.
- No aparecía nada en `storage/logs/laravel.log`: PHP fallaba **antes** de llegar a Laravel.

---

## 3. Causa

Al recibir un archivo, PHP lo guarda primero en una carpeta temporal: la de `upload_tmp_dir` en `php.ini` o, si está vacía, la de las variables `TEMP`/`TMP`.

`php artisan serve` arranca el servidor de PHP **pasándole solo algunas variables de entorno**: la lista `ServeCommand::$passthroughVariables` (`PATH`, `SYSTEMROOT`, `APP_ENV`…). En Windows **`TEMP` y `TMP` no están en esa lista**, así que el servidor no tiene carpeta temporal y rechaza la subida. En producción, o con Herd, no pasa.

---

## 4. Corrección

En `app/Providers/AppServiceProvider.php`:

```php
protected function configureServe(): void
{
    if ($this->app->runningInConsole()) {
        ServeCommand::$passthroughVariables[] = 'TEMP';
        ServeCommand::$passthroughVariables[] = 'TMP';
    }
}
```

`$passthroughVariables` es pública y estática, así que se puede ampliar sin tocar `vendor/`. `runningInConsole()` hace que solo corra con comandos de Artisan, no en cada visita.

**Importante:** la lista se lee cuando arranca `php artisan serve`. **Hay que cortarlo (Ctrl + C) y volver a levantarlo.** El 20/11 el error siguió hasta reiniciar, porque el servidor había arrancado antes del cambio.

**Otras soluciones posibles** (no se usaron): poner `upload_tmp_dir` en el `php.ini` de Herd (afecta a toda la PC, no al proyecto) o usar Herd en vez de `artisan serve`.

---

## 5. Cómo comprobar que no volvió

1. `php artisan serve` recién arrancado.
2. `/crear` → Agregar foto → una imagen JPG.
3. El botón pasa a "Cambiar foto" y en la plantilla **Personalizada** se ve la foto.
4. En F12 → Red, la petición `upload-file` responde **200**.
