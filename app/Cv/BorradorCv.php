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
 *
 * TIPOS PARA PHPSTAN (son comentarios: PHP no los usa, PHPStan sí).
 * Es como un "type" de TypeScript: se define una vez y se usa en los @param y @return.
 *   Item = un registro: columna => texto (ej.: ['position' => 'Dev', ...])
 *   Cv   = el CV entero, con la forma exacta de cada clave
 *
 * @phpstan-type Item array<string, string>
 * @phpstan-type Cv array{datos: Item, experiencias: list<Item>, estudios: list<Item>, habilidades: list<Item>}
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

    /**
     * Todo el CV de una vez (para la vista previa).
     *
     * @return Cv
     */
    public static function todo(): array
    {
        return [
            'datos' => self::datos(),
            'experiencias' => self::lista('experiencias'),
            'estudios' => self::lista('estudios'),
            'habilidades' => self::lista('habilidades'),
        ];
    }

    /** @return Item */
    public static function datos(): array
    {
        return array_merge(self::DATOS_VACIOS, self::soloTextos(session('cv.datos')));
    }

    /** @param  Item  $datos */
    public static function guardarDatos(array $datos): void
    {
        session()->put('cv.datos', array_merge(self::DATOS_VACIOS, array_intersect_key($datos, self::DATOS_VACIOS)));
    }

    /**
     * experiencias, estudios o habilidades.
     *
     * @return list<Item>
     */
    public static function lista(string $nombre): array
    {
        $guardado = session("cv.$nombre");

        if (! is_array($guardado)) {
            return [];
        }

        return array_values(array_map(self::soloTextos(...), $guardado));
    }

    /**
     * La sesión puede devolver cualquier cosa (mixed): de un registro se quedan
     * solo las columnas que son texto. Si no es un array, queda vacío.
     *
     * @return Item
     */
    private static function soloTextos(mixed $valor): array
    {
        if (! is_array($valor)) {
            return [];
        }

        $textos = [];

        foreach ($valor as $columna => $texto) {
            if (is_string($columna) && is_string($texto)) {
                $textos[$columna] = $texto;
            }
        }

        return $textos;
    }

    /**
     * Agrega un ítem al final de una lista.
     *
     * @param  Item  $item
     */
    public static function agregar(string $nombre, array $item): void
    {
        session()->push("cv.$nombre", $item);
    }

    /**
     * Reemplaza el ítem número $indice de una lista (editar una experiencia o un estudio).
     *
     * @param  Item  $item
     */
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
        // explode() siempre devuelve al menos un elemento: $partes[0] existe siempre.
        $seccion = $partes[0];

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
     *
     * @return list<string>
     */
    public static function renglones(?string $descripcion): array
    {
        // preg_split() devuelve false si falla (por ejemplo, con UTF-8 roto): entonces no hay renglones.
        $lineas = preg_split('/\R/u', $descripcion ?? '');

        if ($lineas === false) {
            return [];
        }

        return array_values(collect($lineas)
            ->map(fn ($renglon) => trim(ltrim(trim($renglon), '-•*')))
            ->filter()
            ->all());
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
