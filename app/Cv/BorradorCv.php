<?php

namespace App\Cv;

/**
 * El CV que se está armando en /crear, guardado en la SESIÓN de Laravel
 * (sin base de datos). Es el único lugar que lee y escribe esos datos:
 * los modales, la vista previa y la edición sobre el CV pasan por acá.
 *
 * En la sesión queda así (las claves son las columnas de las tablas):
 *   cv.datos        => full_name, headline, email, phone, location, summary
 *   cv.experiencias => [ [position, company_name, period, description], ... ]
 *   cv.estudios     => [ [title_obtained, institution, period, status], ... ]
 *   cv.habilidades  => [ [name], ... ]
 */
class BorradorCv
{
    public const DATOS_VACIOS = [
        'full_name' => '',
        'headline' => '',
        'email' => '',
        'phone' => '',
        'location' => '',
        'summary' => '',
    ];

    /**
     * Qué se puede editar desde el CV, con su largo máximo y si puede quedar vacío.
     * Es la misma regla que la validación de los modales.
     */
    private const EDITABLES = [
        'datos' => [
            'full_name' => [255, true],
            'headline' => [255, false],
            'email' => [255, true],
            'phone' => [50, false],
            'location' => [255, false],
            'summary' => [2000, false],
        ],
        'experiencias' => [
            'position' => [255, true],
            'company_name' => [255, true],
            'period' => [100, true],
        ],
        'estudios' => [
            'title_obtained' => [255, true],
            'institution' => [255, true],
            'period' => [100, true],
        ],
        'habilidades' => [
            'name' => [100, true],
        ],
    ];

    /** Todo el CV de una vez (para la vista previa). */
    public static function todo(): array
    {
        return [
            'datos' => self::datos(),
            'experiencias' => self::lista('experiencias'),
            'estudios' => self::lista('estudios'),
            'habilidades' => self::lista('habilidades'),
        ];
    }

    public static function datos(): array
    {
        return array_merge(self::DATOS_VACIOS, session('cv.datos', []));
    }

    public static function guardarDatos(array $datos): void
    {
        session()->put('cv.datos', array_merge(self::DATOS_VACIOS, array_intersect_key($datos, self::DATOS_VACIOS)));
    }

    /** experiencias, estudios o habilidades. */
    public static function lista(string $nombre): array
    {
        return array_values(session("cv.$nombre", []));
    }

    /** Agrega un ítem al final de una lista. */
    public static function agregar(string $nombre, array $item): void
    {
        session()->push("cv.$nombre", $item);
    }

    /** Reemplaza el ítem número $indice de una lista (editar una experiencia o un estudio). */
    public static function reemplazar(string $nombre, int $indice, array $item): void
    {
        $lista = self::lista($nombre);

        if (isset($lista[$indice])) {
            $lista[$indice] = $item;
            session()->put("cv.$nombre", $lista);
        }
    }

    /** Quita el ítem número $indice de una lista. Los de después se corren un lugar. */
    public static function quitar(string $nombre, int $indice): void
    {
        $lista = self::lista($nombre);

        if (isset($lista[$indice])) {
            unset($lista[$indice]);
            session()->put("cv.$nombre", array_values($lista));
        }
    }

    /**
     * Edición desde el CV: $campo dice qué cambiar, por ejemplo
     *   "datos.full_name", "experiencias.2.position",
     *   "experiencias.2.description.1" (el renglón 1 de la descripción).
     *
     * Devuelve false si el campo no existe o el texto no vale (entonces no cambia nada).
     */
    public static function editar(string $campo, string $texto): bool
    {
        $partes = explode('.', $campo);
        $seccion = $partes[0] ?? '';

        // Un solo renglón, sin espacios de más (el resumen también: el CV lo muestra en un párrafo).
        $texto = trim(preg_replace('/\s+/u', ' ', $texto));

        // ---- Renglón de la descripción de una experiencia ----
        if ($seccion === 'experiencias' && count($partes) === 4 && $partes[2] === 'description') {
            return self::editarRenglon((int) $partes[1], (int) $partes[3], $texto);
        }

        // ---- Datos personales: datos.{columna} ----
        if ($seccion === 'datos' && count($partes) === 2) {
            $columna = $partes[1];

            if (! self::textoValido('datos', $columna, $texto)) {
                return false;
            }

            if ($columna === 'email' && ! filter_var($texto, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $datos = self::datos();
            $datos[$columna] = $texto;
            self::guardarDatos($datos);

            return true;
        }

        // ---- Listas: {seccion}.{índice}.{columna} ----
        if (count($partes) === 3 && ctype_digit($partes[1])) {
            [$seccion, $indice, $columna] = [$partes[0], (int) $partes[1], $partes[2]];
            $lista = self::lista($seccion);

            if (! isset($lista[$indice]) || ! self::textoValido($seccion, $columna, $texto)) {
                return false;
            }

            $lista[$indice][$columna] = $texto;
            session()->put("cv.$seccion", $lista);

            return true;
        }

        return false;
    }

    /**
     * Los renglones de una descripción, tal como los muestra el CV:
     * sin renglones vacíos y sin el guion o la viñeta del principio.
     */
    public static function renglones(?string $descripcion): array
    {
        return collect(preg_split('/\R/u', (string) $descripcion))
            ->map(fn ($renglon) => trim(ltrim(trim($renglon), '-•*')))
            ->filter()
            ->values()
            ->all();
    }

    private static function editarRenglon(int $indice, int $renglon, string $texto): bool
    {
        $experiencias = self::lista('experiencias');

        if (! isset($experiencias[$indice])) {
            return false;
        }

        $renglones = self::renglones($experiencias[$indice]['description'] ?? '');

        if (! array_key_exists($renglon, $renglones)) {
            return false;
        }

        // Borrar todo el texto de un renglón lo quita (pero tiene que quedar al menos uno).
        if ($texto === '') {
            unset($renglones[$renglon]);
        } else {
            $renglones[$renglon] = $texto;
        }

        $descripcion = implode("\n", $renglones);

        if ($descripcion === '' || mb_strlen($descripcion) > 2000) {
            return false;
        }

        $experiencias[$indice]['description'] = $descripcion;
        session()->put('cv.experiencias', $experiencias);

        return true;
    }

    private static function textoValido(string $seccion, string $columna, string $texto): bool
    {
        if (! isset(self::EDITABLES[$seccion][$columna])) {
            return false;
        }

        [$maximo, $obligatorio] = self::EDITABLES[$seccion][$columna];

        if ($obligatorio && $texto === '') {
            return false;
        }

        return mb_strlen($texto) <= $maximo;
    }
}
