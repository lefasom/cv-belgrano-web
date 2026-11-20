<?php

namespace App\Cv;

/**
 * Achica la foto del CV con GD (la librería de imágenes de PHP).
 *
 * Es lo mismo que hace AvatarPicker.tsx en la app con ImageManipulator:
 * recorte cuadrado (aspect 1:1), resize y JPEG comprimido.
 * Así una foto de celular de varios MB queda en unos pocos KB.
 *
 * Devuelve la foto como "data URI" (data:image/jpeg;base64,...): un texto que el
 * navegador muestra en un <img> sin guardar ningún archivo.
 */
class FotoCv
{
    /** Lado final de la foto. En el CV se ve de ~110px: 500 alcanza para imprimir nítido. */
    public const LADO = 500;

    /** Calidad del JPEG (0-100). La app usa compress: 0.85. */
    public const CALIDAD = 82;

    /** Más que esto no se abre: una imagen enorme gastaría demasiada memoria. */
    public const MAX_PIXELES = 30_000_000;

    /** Devuelve null si el archivo no es una imagen que se pueda leer. */
    public static function comprimir(string $ruta): ?string
    {
        $info = @getimagesize($ruta);

        if (! $info || $info[0] * $info[1] > self::MAX_PIXELES) {
            return null;
        }

        // file_get_contents() devuelve false si no puede leer el archivo.
        $contenido = file_get_contents($ruta);

        if ($contenido === false) {
            return null;
        }

        $original = @imagecreatefromstring($contenido);

        if (! $original) {
            return null;
        }

        $original = self::enderezar($original, $ruta);

        // Recorte cuadrado del centro (como aspect [1, 1]).
        $ancho = imagesx($original);
        $alto = imagesy($original);
        $lado = min($ancho, $alto);
        $x = intdiv($ancho - $lado, 2);
        $y = intdiv($alto - $lado, 2);
        $final = min(self::LADO, $lado);

        $foto = imagecreatetruecolor($final, $final);
        // Fondo blanco por si el PNG tiene partes transparentes (el JPEG no las soporta).
        // imagecolorallocate() devuelve false si no puede crear el color: sin fondo no se sigue.
        $blanco = imagecolorallocate($foto, 255, 255, 255);

        if ($blanco === false) {
            return null;
        }

        imagefill($foto, 0, 0, $blanco);
        imagecopyresampled($foto, $original, 0, 0, $x, $y, $final, $final, $lado, $lado);

        ob_start();
        imagejpeg($foto, null, self::CALIDAD);
        $jpeg = ob_get_clean();

        imagedestroy($original);
        imagedestroy($foto);

        return 'data:image/jpeg;base64,'.base64_encode($jpeg);
    }

    /** Las fotos del celular vienen "acostadas" con un dato EXIF de rotación: se corrige. */
    private static function enderezar(\GdImage $imagen, string $ruta): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $imagen;
        }

        $orientacion = (@exif_read_data($ruta) ?: [])['Orientation'] ?? 1;

        $grados = match ($orientacion) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($grados === 0) {
            return $imagen;
        }

        // imagerotate() devuelve false si falla: entonces queda la foto como vino.
        return imagerotate($imagen, $grados, 0) ?: $imagen;
    }
}
