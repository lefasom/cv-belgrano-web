<?php

namespace App\Cv;

/**
 * Prepara el CV para las plantillas (ATS y Personalizada).
 *
 * Hace el "filtrado de CV data" de la app: limpia los textos y saca todo lo vacío,
 * así cada plantilla solo pregunta "¿hay algo?" y lo muestra.
 *
 * Cada ítem conserva su índice ORIGINAL ('i'): con él la plantilla arma
 * data-cv-campo (ej.: "experiencias.2.position") para editar desde el CV.
 * Es lo mismo que el originalIndex de los templates de la app.
 *
 * El tipo Cv viene de BorradorCv (@phpstan-import-type lo trae, como un import de TypeScript).
 *
 * @phpstan-import-type Cv from BorradorCv
 *
 * @phpstan-type Plantilla array{
 *     nombre: string, profesion: string, email: string, telefono: string, ubicacion: string, resumen: string,
 *     experiencias: list<array{i: int, puesto: string, empresa: string, periodo: string, tareas: list<string>}>,
 *     estudios: list<array{i: int, titulo: string, institucion: string, periodo: string, estado: string}>,
 *     habilidades: list<array{i: int, nombre: string}>,
 *     foto: string|null,
 *     vacio: bool,
 * }
 */
class DatosPlantilla
{
    /**
     * @param  Cv  $cv
     * @return Plantilla
     */
    public static function desde(array $cv, ?string $foto = null): array
    {
        $limpio = fn (string $valor): string => trim($valor);
        $datos = array_map($limpio, $cv['datos']);

        // array_values() deja una lista (claves 0, 1, 2… sin huecos) después de filter(),
        // que saca elementos y deja huecos en las claves.
        $experiencias = array_values(collect($cv['experiencias'])
            ->map(fn ($e, $i) => [
                'i' => $i,
                'puesto' => $limpio($e['position'] ?? ''),
                'empresa' => $limpio($e['company_name'] ?? ''),
                'periodo' => $limpio($e['period'] ?? ''),
                // Cada renglón de la descripción es una viñeta.
                'tareas' => BorradorCv::renglones($e['description'] ?? ''),
            ])
            ->filter(fn ($e) => $e['puesto'] || $e['empresa'] || $e['periodo'] || $e['tareas'])
            ->all());

        $estudios = array_values(collect($cv['estudios'])
            ->map(fn ($e, $i) => [
                'i' => $i,
                'titulo' => $limpio($e['title_obtained'] ?? ''),
                'institucion' => $limpio($e['institution'] ?? ''),
                'periodo' => $limpio($e['period'] ?? ''),
                'estado' => self::estado($e['status'] ?? ''),
            ])
            ->filter(fn ($e) => $e['titulo'] || $e['institucion'] || $e['periodo'])
            ->all());

        $habilidades = array_values(collect($cv['habilidades'])
            ->map(fn ($h, $i) => ['i' => $i, 'nombre' => $limpio($h['name'] ?? '')])
            ->filter(fn ($h) => $h['nombre'] !== '')
            ->all());

        $resultado = [
            'nombre' => $datos['full_name'] ?? '',
            'profesion' => $datos['headline'] ?? '',
            'email' => $datos['email'] ?? '',
            'telefono' => $datos['phone'] ?? '',
            'ubicacion' => $datos['location'] ?? '',
            'resumen' => $datos['summary'] ?? '',
            'experiencias' => $experiencias,
            'estudios' => $estudios,
            'habilidades' => $habilidades,
            'foto' => $foto,
        ];

        // ¿Hay algo para mostrar? (la foto sola no cuenta: el ATS no la usa)
        $resultado['vacio'] = ! collect($resultado)->except(['foto'])->filter()->count();

        return $resultado;
    }

    /**
     * En la base el estado va en inglés (Completed / In Progress); acá se traduce.
     * __() puede devolver un array (si la clave es un grupo) o la clave misma si
     * no existe: solo se usa si es un texto de verdad.
     */
    private static function estado(string $status): string
    {
        if ($status === '') {
            return '';
        }

        $clave = "cv.estado.$status";
        $texto = __($clave);

        return is_string($texto) && $texto !== $clave ? $texto : '';
    }
}
