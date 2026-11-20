<?php

/*
    Textos del CV y del creador (/crear).
    - secciones / estado: lo que se imprime en el CV (las plantillas).
    - creador: los botones, las herramientas y la vista previa de la página.
    El idioma lo elige el sitio: CV y página van siempre en el mismo idioma.
*/

return [
    'secciones' => [
        'perfil' => 'Perfil profissional',
        'experiencia' => 'Experiência profissional',
        'educacion' => 'Educação e cursos',
        'habilidades' => 'Habilidades e competências',
        'contacto' => 'Contato',
    ],


    'estado' => [
        'Completed' => 'Concluído',
        'In Progress' => 'Em andamento',
    ],

    'creador' => [
        'botonesAria' => 'Seções do currículo',
        'datos' => 'Dados pessoais',
        'experiencia' => 'Experiência',
        'educacion' => 'Educação',
        'habilidades' => 'Habilidades',
        'nueva' => 'Nova',
        'eliminar' => 'Excluir',
        'agregar' => 'Adicionar',
        'listo' => 'Pronto',

        'plantillasAria' => 'Modelo do currículo',
        'plantillaAts' => 'Modelo ATS',
        'plantillaPersonalizada' => 'Personalizado',

        'vistaAria' => 'Prévia do currículo',
        'vacio' => 'Seu currículo aparece aqui conforme você preenche as seções.',
        'editarAyuda' => 'Toque em um texto do currículo para editá-lo ali mesmo.',
        'descargar' => 'Baixar PDF',

        'herramientas' => 'Ferramentas',
        'foto' => 'Foto',
        'fotoAgregar' => 'Adicionar foto',
        'fotoCambiar' => 'Trocar foto',
        'fotoQuitar' => 'Remover',
        'fotoSubiendo' => 'Enviando…',
        'fotoAyuda' => 'Só aparece no modelo Personalizado. Não é salva: ao recarregar a página, ela some.',
        'fotoNoImagen' => 'Escolha uma imagem JPG, PNG ou WEBP.',
        'fotoPesada' => 'A imagem é muito pesada (máximo 10 MB).',
        'letra' => 'Tamanho da letra',
        'letraMenos' => 'Diminuir a letra',
        'letraMas' => 'Aumentar a letra',
        'mover' => 'Zoom e mover',
        'zoom' => 'Zoom',
        'zoomNormal' => 'Voltar ao tamanho normal',
        'desplazar' => 'Mover',
        'moverAyuda' => 'Escolha a lupa ou a mão e use com Ctrl + clique arrastando sobre o currículo (sem Ctrl, o clique edita o texto). Ctrl + roda também faz zoom. No celular: faça pinça para o zoom e arraste para se mover.',
    ],
];
