<?php

/*
    Textos del CV y del creador (/crear).
    - secciones / estado: lo que se imprime en el CV (las plantillas).
    - creador: los botones, las herramientas y la vista previa de la página.
    El idioma lo elige el sitio: CV y página van siempre en el mismo idioma.
*/

return [
    'secciones' => [
        'perfil' => 'Professional profile',
        'experiencia' => 'Work experience',
        'educacion' => 'Education & courses',
        'habilidades' => 'Skills & competencies',
        'contacto' => 'Contact',
    ],


    'estado' => [
        'Completed' => 'Completed',
        'In Progress' => 'In progress',
    ],

    'creador' => [
        'botonesAria' => 'CV sections',
        'datos' => 'Personal details',
        'experiencia' => 'Experience',
        'educacion' => 'Education',
        'habilidades' => 'Skills',
        'nueva' => 'New',
        'eliminar' => 'Delete',
        'agregar' => 'Add',
        'listo' => 'Done',

        'plantillasAria' => 'CV template',
        'plantillaAts' => 'ATS template',
        'plantillaPersonalizada' => 'Custom',

        'vistaAria' => 'CV preview',
        'vacio' => 'Your CV shows up here as you fill in the sections.',
        'editarAyuda' => 'Tap any text in the CV to edit it right there.',
        'descargar' => 'Download PDF',

        'herramientas' => 'Tools',
        'foto' => 'Photo',
        'fotoAgregar' => 'Add photo',
        'fotoCambiar' => 'Change photo',
        'fotoQuitar' => 'Remove',
        'fotoSubiendo' => 'Uploading…',
        'fotoAyuda' => 'Only shown in the Custom template. Not saved: reloading the page removes it.',
        'fotoNoImagen' => 'Choose a JPG, PNG or WEBP image.',
        'fotoPesada' => 'The image is too large (10 MB max).',
        'letra' => 'Font size',
        'letraMenos' => 'Smaller text',
        'letraMas' => 'Larger text',
        'mover' => 'Zoom and pan',
        'zoom' => 'Zoom',
        'zoomNormal' => 'Back to normal size',
        'desplazar' => 'Pan',
        'moverAyuda' => 'Pick the magnifier or the hand and use it with Ctrl + click and drag on the CV (without Ctrl, a click edits the text). Ctrl + mouse wheel also zooms. On a phone: pinch to zoom and drag to move around.',
    ],
];
