<?php

/*
 * VERIFICAR TRADUCCIONES
 * Uso (desde la carpeta del proyecto):  php herramientas/verificar_traducciones.php
 *
 * Las vistas usan los textos como array ($t = __('cv'); $t['creador']['foto']).
 * Si falta una clave en un idioma, esa página da error 500. Este script lo revisa
 * antes de que pase. Ver documentacion/CENTRO_DE_MANDO/06_HEADER_LAYOUT_E_IDIOMAS.md §3.
 *
 * 1. Que los archivos de lang/ tengan las mismas claves en los tres idiomas
 *    y los mismos comodines (:nombre).
 * 2. Que cada clave usada en resources/views exista en los tres idiomas.
 *
 * Termina con código 1 si encuentra problemas (sirve para usarlo antes de un commit).
 */

$base = dirname(__DIR__);
$idiomas = ['es', 'en', 'pt'];
$problemas = 0;

/** Convierte ['menu' => ['crear' => 'Crear CV']] en ['menu.crear' => 'Crear CV']. */
function aplanar(array $textos, string $prefijo = ''): array
{
    $plano = [];
    foreach ($textos as $clave => $valor) {
        $completa = $prefijo === '' ? (string) $clave : "$prefijo.$clave";
        if (is_array($valor)) {
            $plano += aplanar($valor, $completa);
        } else {
            $plano[$completa] = $valor;
        }
    }

    return $plano;
}

// ============ 1. Los tres idiomas tienen las mismas claves ============
$archivos = array_map(fn ($ruta) => basename($ruta, '.php'), glob("$base/lang/es/*.php"));

foreach ($archivos as $archivo) {
    $planos = [];
    foreach ($idiomas as $idioma) {
        $ruta = "$base/lang/$idioma/$archivo.php";
        if (! file_exists($ruta)) {
            echo "FALTA EL ARCHIVO lang/$idioma/$archivo.php\n";
            $problemas++;

            continue 2;
        }
        $planos[$idioma] = aplanar(require $ruta);
    }

    $todas = array_unique(array_merge(...array_values(array_map('array_keys', $planos))));

    foreach ($todas as $clave) {
        foreach ($idiomas as $idioma) {
            if (! array_key_exists($clave, $planos[$idioma])) {
                echo "FALTA en lang/$idioma/$archivo.php: $clave\n";
                $problemas++;
            }
        }

        // Los comodines (:nombre) tienen que ser los mismos en los tres idiomas.
        if (isset($planos['es'][$clave])) {
            preg_match_all('/:\w+/', (string) $planos['es'][$clave], $enEspanol);
            foreach (['en', 'pt'] as $idioma) {
                if (isset($planos[$idioma][$clave])) {
                    preg_match_all('/:\w+/', (string) $planos[$idioma][$clave], $enOtro);
                    if ($enEspanol[0] != $enOtro[0]) {
                        echo "COMODÍN distinto en lang/$idioma/$archivo.php: $clave\n";
                        $problemas++;
                    }
                }
            }
        }
    }
}

// ============ 2. Cada clave usada en las vistas existe ============
$vistas = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("$base/resources/views"));
$comprobaciones = 0;

foreach ($vistas as $vista) {
    if (! str_ends_with($vista, '.blade.php')) {
        continue;
    }
    $codigo = file_get_contents($vista);
    // Ruta corta para el mensaje (en Windows se mezclan \ y /: se pasan todas a /).
    $nombre = str_replace(str_replace('\\', '/', $base).'/', '', str_replace('\\', '/', (string) $vista));

    // Las variables de textos del archivo: @php($t = __('cv')) o $t = __('cv'); en un bloque.
    preg_match_all('/\$(\w+) = __\(\'(\w+)\'\)[);]/', $codigo, $definidas, PREG_SET_ORDER);
    $variables = [];
    foreach ($definidas as $d) {
        $variables[$d[1]] = $d[2];
    }

    // Los usos: $t['creador']['foto'] y __('cv')['creador']['foto'].
    $usos = [];
    foreach ($variables as $variable => $archivo) {
        preg_match_all('/\$'.$variable.'((?:\[\'[^\']+\'\])+)/', $codigo, $m);
        foreach ($m[1] as $cadena) {
            $usos[] = [$archivo, $cadena];
        }
    }
    preg_match_all('/__\(\'(\w+)\'\)((?:\[\'[^\']+\'\])+)/', $codigo, $m, PREG_SET_ORDER);
    foreach ($m as $uso) {
        $usos[] = [$uso[1], $uso[2]];
    }

    // $t o $l usadas sin definir en el archivo (un componente no ve el $t de quien lo usa).
    preg_match_all('/\$(t|l)\[/', $codigo, $sueltas);
    foreach (array_unique($sueltas[1]) as $variable) {
        if (! isset($variables[$variable])) {
            echo "SIN DEFINIR \$$variable en $nombre\n";
            $problemas++;
        }
    }

    foreach ($usos as [$archivo, $cadena]) {
        preg_match_all('/\[\'([^\']+)\'\]/', $cadena, $claves);
        foreach ($idiomas as $idioma) {
            $comprobaciones++;
            $textos = require "$base/lang/$idioma/$archivo.php";
            foreach ($claves[1] as $clave) {
                if (! is_array($textos) || ! array_key_exists($clave, $textos)) {
                    echo "FALTA en lang/$idioma/$archivo.php: ".implode('.', $claves[1])." (la usa $nombre)\n";
                    $problemas++;

                    continue 2;
                }
                $textos = $textos[$clave];
            }
        }
    }
}

echo "Claves usadas comprobadas: $comprobaciones. Problemas: $problemas\n";

exit($problemas > 0 ? 1 : 0);
