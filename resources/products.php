<?php

/*
|--------------------------------------------------------------------------
| Catalogue de l'écosystème — SOURCE DE VÉRITÉ
|--------------------------------------------------------------------------
| - La clé du tableau est l'identifiant stable du produit : ne jamais la
|   renommer une fois déployée (elle sert à ECOSYSTEM_CURRENT et aux UTM).
| - L'ordre du tableau = l'ordre d'affichage.
| - "logo" accepte :
|     'laredac.svg'                      → fichier dans resources/logos
|     'https://…/logo.png'               → URL distante
|     ['svg' => …, 'url' => …, 'text' => …, 'alt' => …]  → plusieurs formats
|   "text" (initiales ou emoji) est généré depuis le nom si absent.
| - "active" => false : produit gardé au catalogue mais jamais affiché.
|
| Après modification : composer test, puis tag d'une nouvelle version.
*/

return [

    'laredac' => [
        'name' => "LaRédac'",
        'url' => 'https://laredac.ai',
        'tagline' => 'Vos posts LinkedIn, écrits à partir d’une interview.',
        'description' => 'L’app vous interviewe, creuse vos idées et propose plusieurs versions de posts prêtes à publier.',
        'logo' => [
            'svg' => 'laredac.svg',
            'url' => 'https://laredac.ai/logo.svg', // TODO : vérifier le chemin
            'text' => 'LR',
        ],
        'color' => '#0A66C2', // TODO : couleur de marque
        'category' => 'linkedin',
        'tags' => ['linkedin', 'rédaction', 'ia'],
    ],

    'notionscheduler' => [
        'name' => 'NotionScheduler',
        'url' => 'https://notionscheduler.app',
        'tagline' => 'Planifiez vos posts réseaux sociaux depuis Notion.',
        'logo' => ['svg' => 'notionscheduler.svg', 'text' => 'NS'],
        'category' => 'social',
        'tags' => ['notion', 'planification', 'réseaux sociaux'],
    ],

    'abrege' => [
        'name' => 'Abrège',
        'url' => 'https://abrege.app', // TODO : vérifier le domaine
        'tagline' => 'Vos vocaux WhatsApp transcrits et résumés.',
        'logo' => ['svg' => 'abrege.svg', 'text' => 'A'],
        'category' => 'productivité',
        'tags' => ['whatsapp', 'transcription'],
    ],

    'mission-monitor' => [
        'name' => 'Mission Monitor', // TODO : nom définitif
        'url' => 'https://mission-monitor.example', // TODO : domaine
        'tagline' => 'Les missions freelance publiées sur LinkedIn, dès qu’elles sortent.',
        'logo' => ['text' => '🎯'],
        'category' => 'freelance',
        'tags' => ['freelance', 'linkedin', 'veille'],
        'active' => false, // en développement : pas encore affiché
    ],

];
