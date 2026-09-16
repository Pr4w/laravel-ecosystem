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
| - "prefer" => 'svg' | 'url' | 'text' : format à afficher en priorité pour ce
|   produit. À mettre quand la marque EST un emoji ('text'). Sans cette clé,
|   l'ordre par défaut s'applique : svg, puis url, puis text.
| - "active" => false : produit gardé au catalogue mais jamais affiché.
|
| Après modification : composer test, puis tag d'une nouvelle version.
*/

return [

    'laredac' => [
        'name' => 'LaRédac',
        'url' => 'https://laredac.ai',
        'tagline' => 'Transforme une conversation de deux minutes en post LinkedIn prêt à publier.',
        'description' => "L'IA mène l'entretien à la voix ou au clavier, puis rédige trois versions calées sur la voix de l'auteur. Édition, planification et publication se font au même endroit.",
        'logo' => [
            'svg' => 'laredac.svg',
            'url' => 'https://laredac.ai/web-app-manifest-512x512.png',
            'text' => 'LR',
        ],
        'color' => '#ef852e',
        'category' => 'linkedin',
        'tags' => ['linkedin', 'rédaction', 'ia', 'entretien vocal', 'personal branding'],
    ],

    'notionscheduler' => [
        'name' => 'NotionScheduler',
        'url' => 'https://notionscheduler.app',
        'tagline' => 'Planifiez vos publications sociales sans quitter Notion.',
        'description' => 'Sept réseaux — Instagram, LinkedIn, X, TikTok, Facebook, Threads, YouTube — publiés depuis une base Notion. Le calendrier éditorial reste là où l\'équipe écrit déjà.',
        'logo' => [
            'svg' => 'notionscheduler.svg',
            'url' => 'https://notionscheduler.app/favicon.png', // TODO : à vérifier — le favicon servi est encore l'ancien mark « S » saumon, pas le badge calendrier du SVG
            'text' => 'NS',
        ],
        'color' => '#F76C1F',
        'category' => 'social',
        'tags' => ['notion', 'réseaux sociaux', 'planification', 'calendrier éditorial', 'automatisation'],
    ],

    'abrege' => [
        'name' => 'Abrège',
        'url' => 'https://abrege.app',
        'tagline' => 'Résume vos messages vocaux WhatsApp en quelques secondes.',
        'description' => 'Il suffit de transférer le vocal au contact Abrège pour recevoir un texte clair. Aucune application à installer, 5 résumés offerts chaque mois.',
        'logo' => [
            'text' => '🍿',      // la marque EST l'emoji
            'prefer' => 'text',
            'svg' => 'abrege.svg', // repli monochrome pour un site qui ne veut pas d'emoji
            'url' => 'https://abrege.app/images/LogoSquare.png',
        ],
        'color' => '#25D366', // TODO : à vérifier
        'category' => 'whatsapp', // TODO : à vérifier
        'tags' => ['whatsapp', 'vocal', 'transcription', 'résumé', 'ia'],
    ],

    'cherche-mission' => [
        'name' => 'Cherche Mission',
        'url' => 'https://cherchemission.fr',
        'tagline' => 'Les missions freelance publiées sur LinkedIn, par e-mail avant les autres.',
        'description' => "Le flux est lu en continu, les offres salariées et les profils disponibles écartés. L'alerte part en temps réel ; l'abonnement débloque le nom du décideur et le lien vers sa publication.",
        'logo' => [
            'text' => '👀',      // la marque EST l'emoji
            'prefer' => 'text',
            'svg' => 'cherche-mission.svg', // repli monochrome (redessin, pas un asset du dépôt)
            'url' => 'https://cherchemission.fr/logo-512.png',
        ],
        'color' => '#f59e0b',
        'category' => 'freelance',
        'tags' => ['linkedin', 'veille', 'missions', 'alertes', 'tjm'],
    ],

];
