<?php

/*
    Textos del CV y del creador (/crear).
    - secciones / ats / estado: lo que se imprime en el CV (secciones = Personalizada, ats = ATS).
    - creador: los botones, las herramientas y la vista previa de la página.
    El idioma lo elige el sitio: CV y página van siempre en el mismo idioma.
*/

return [
    'secciones' => [
        'perfil' => 'Perfil profesional',
        'experiencia' => 'Experiencia laboral',
        'educacion' => 'Educación & cursos',
        'habilidades' => 'Habilidades & competencias',
        'contacto' => 'Contacto',
    ],

    // Títulos de la plantilla ATS (traductorTemplateATS de la app): estándar y sin "&",
    // para que un sistema ATS reconozca cada sección sin dudas.
    'ats' => [
        'perfil' => 'Resumen Profesional',
        'experiencia' => 'Experiencia',
        'educacion' => 'Educación',
        'habilidades' => 'Habilidades',
    ],

    // En la base el estado se guarda en inglés; acá se traduce para el CV.
    'estado' => [
        'Completed' => 'Terminado',
        'In Progress' => 'En curso',
    ],

    'creador' => [
        'botonesAria' => 'Secciones del CV',
        'datos' => 'Datos personales',
        'experiencia' => 'Experiencia',
        'educacion' => 'Educación',
        'habilidades' => 'Habilidades',
        'nueva' => 'Nueva',
        'eliminar' => 'Eliminar',
        'agregar' => 'Agregar',
        'listo' => 'Listo',

        'plantillasAria' => 'Plantilla del CV',
        'plantillaAts' => 'Plantilla ATS',
        'plantillaPersonalizada' => 'Personalizada',

        'vistaAria' => 'Vista previa del CV',
        'vacio' => 'Tu CV aparece acá a medida que completás las secciones.',
        'editarAyuda' => 'Tocá un texto del CV para editarlo ahí mismo.',
        'descargar' => 'Descargar PDF',

        'herramientas' => 'Herramientas',
        'foto' => 'Foto',
        'fotoAgregar' => 'Agregar foto',
        'fotoCambiar' => 'Cambiar foto',
        'fotoQuitar' => 'Quitar',
        'fotoSubiendo' => 'Subiendo…',
        'fotoAyuda' => 'Solo se ve en la plantilla Personalizada. No se guarda: al recargar la página se quita.',
        'fotoNoImagen' => 'Elegí una imagen JPG, PNG o WEBP.',
        'fotoPesada' => 'La imagen pesa demasiado (máximo 10 MB).',
        'letra' => 'Tamaño de letra',
        'letraMenos' => 'Achicar la letra',
        'letraMas' => 'Agrandar la letra',
        'mover' => 'Zoom y desplazar',
        'zoom' => 'Zoom',
        'zoomNormal' => 'Volver al tamaño normal',
        'desplazar' => 'Desplazar',
        'moverAyuda' => 'Elegí la lupa o la mano y usala con Ctrl + clic arrastrando sobre el CV (sin Ctrl, el clic edita el texto). Ctrl + rueda también hace zoom. En el celular: pellizcá para el zoom y arrastrá para moverte.',
    ],
];
