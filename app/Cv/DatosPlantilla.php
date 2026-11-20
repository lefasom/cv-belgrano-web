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
 */
class DatosPlantilla
{
    public static function desde(array $cv, ?string $foto = null): array
    {
        $limpio = fn ($valor) => trim((string) ($valor ?? ''));
        $datos = array_map($limpio, $cv['datos'] ?? []);

        $experiencias = collect($cv['experiencias'] ?? [])
            ->map(fn ($e, $i) => [
                'i' => $i,
                'puesto' => $limpio($e['position'] ?? ''),
                'empresa' => $limpio($e['company_name'] ?? ''),
                'periodo' => $limpio($e['period'] ?? ''),
                // Cada renglón de la descripción es una viñeta.
                'tareas' => BorradorCv::renglones($e['description'] ?? ''),
            ])
            ->filter(fn ($e) => $e['puesto'] || $e['empresa'] || $e['periodo'] || $e['tareas'])
            ->values()
            ->all();

        $estudios = collect($cv['estudios'] ?? [])
            ->map(fn ($e, $i) => [
                'i' => $i,
                'titulo' => $limpio($e['title_obtained'] ?? ''),
                'institucion' => $limpio($e['institution'] ?? ''),
                'periodo' => $limpio($e['period'] ?? ''),
                // En la base va en inglés (Completed / In Progress); acá se traduce.
                'estado' => filled($e['status'] ?? null) ? __('cv.estado.'.$e['status']) : '',
            ])
            ->filter(fn ($e) => $e['titulo'] || $e['institucion'] || $e['periodo'])
            ->values()
            ->all();

        $habilidades = collect($cv['habilidades'] ?? [])
            ->map(fn ($h, $i) => ['i' => $i, 'nombre' => $limpio(is_array($h) ? ($h['name'] ?? '') : $h)])
            ->filter(fn ($h) => $h['nombre'] !== '')
            ->values()
            ->all();

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
}
