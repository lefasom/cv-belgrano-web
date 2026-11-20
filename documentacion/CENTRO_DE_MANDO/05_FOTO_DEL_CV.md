# 05_FOTO_DEL_CV

**Última actualización:** 20/11/2026

> Cómo se sube y se achica la foto, y por qué no se guarda.

---

## 1. Resumen

| | |
| --- | --- |
| Dónde se usa | Solo en la plantilla **Personalizada** (la ATS no lleva foto) |
| Componente | `resources/views/livewire/creador/foto.blade.php` |
| Compresión | `app/Cv/FotoCv.php` con **GD** (librería de imágenes de PHP) |
| Dónde queda | En la propiedad `$foto` de `vista-previa`, solo mientras la página está abierta |
| ¿Se guarda? | **No**: ni en la base de datos ni en la sesión. Al recargar se pierde (decisión de Leo) |

---

## 2. El recorrido

```
1. <label> "Agregar foto" abre el selector de archivos (el <input type="file"> está escondido)
2. wire:model="archivo"     Livewire sube el archivo a storage/app/private/livewire-tmp
3. updatedArchivo()          Livewire lo llama solo al terminar la subida ("updated" + nombre de la propiedad)
4. validate()                imagen JPG, PNG o WEBP de hasta 10 MB
5. FotoCv::comprimir()       recorte cuadrado + 500×500 + JPEG al 82 %
6. $this->archivo->delete()  se borra el original de la carpeta temporal
7. dispatch('foto-cambiada', foto: 'data:image/jpeg;base64,…')
8. vista-previa::ponerFoto() la acepta solo si empieza con data:image/jpeg;base64, y pesa menos de 400 KB
```

"Quitar" manda el mismo evento con `foto: null`.

---

## 3. `FotoCv::comprimir()`

Es lo que hace `AvatarPicker.tsx` en la app con `ImageManipulator` (`aspect [1, 1]`, 800×800, `compress: 0.85`):

| Paso | Función de GD | Por qué |
| ---- | ------------- | ------- |
| Mirar el tamaño sin abrirla | `getimagesize` | Más de 30 millones de píxeles no se abre: gastaría demasiada memoria |
| Abrirla | `imagecreatefromstring` | Sirve para JPG, PNG y WEBP |
| Enderezarla | `exif_read_data` + `imagerotate` | Las fotos del celular vienen "acostadas" con un dato EXIF de rotación |
| Recorte cuadrado del centro | `imagecopyresampled` | La foto del CV es cuadrada |
| Fondo blanco | `imagefill` | Un PNG transparente quedaría negro en JPEG |
| Guardar como JPEG | `imagejpeg` + `ob_start` | Calidad 82; se captura en memoria, sin archivo |
| Devolver un *data URI* | `base64_encode` | Un texto que el `<img>` muestra sin guardar ningún archivo |

**500 px y no 800 como la app:** en el CV la foto se ve de unos 110 px; 500 alcanza para imprimir nítido y pesa menos, porque viaja en cada actualización de Livewire. En la prueba, una foto de 3000×2000 (520 KB) quedó en unos 26 KB.

---

## 4. Problema conocido en Windows

Con `php artisan serve` en Windows, la subida fallaba con **422** ("The archivo failed to upload"). La causa y el arreglo están en `../AUDITORIAS/01_ERROR_422_AL_SUBIR_LA_FOTO.md`. **Después de ese cambio hay que reiniciar `php artisan serve`.**

---

## 5. Historial de cambios

| Fecha | Cambio |
| ----- | ------ |
| 20/11/2026 | Creación |
