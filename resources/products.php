<?php

/*
|--------------------------------------------------------------------------
| Catalogue de l'écosystème — SOURCE DE VÉRITÉ
|--------------------------------------------------------------------------
| - La clé du tableau est l'identifiant stable du produit : ne jamais la
|   renommer une fois déployée (elle sert à ECOSYSTEM_CURRENT et aux UTM).
| - L'ordre du tableau = l'ordre d'affichage.
| - "tagline" et "description" acceptent :
|     'Une phrase.'                      → la même dans toutes les langues
|     ['fr' => '…', 'en' => '…']         → une par langue
|   La langue de la requête est utilisée, puis celle de repli de l'app, puis
|   la première disponible : un site n'affiche jamais un texte vide.
| - "name" n'est pas traduit : une marque garde son nom.
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
        'tagline' => [
            'fr' => 'Transforme une conversation de deux minutes en post LinkedIn prêt à publier.',
            'en' => 'Turns a two-minute conversation into a LinkedIn post ready to publish.',
        ],
        'description' => [
            'fr' => "L'IA mène l'entretien à la voix ou au clavier, puis rédige trois versions calées sur la voix de l'auteur. Édition, planification et publication se font au même endroit.",
            'en' => "The AI runs the interview by voice or keyboard, then writes three versions tuned to the author's voice. Editing, scheduling and publishing all happen in one place.",
        ],
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
        'tagline' => [
            'fr' => 'Planifiez vos publications sociales sans quitter Notion.',
            'en' => 'Schedule your social posts without leaving Notion.',
        ],
        'description' => [
            'fr' => "Sept réseaux — Instagram, LinkedIn, X, TikTok, Facebook, Threads, YouTube — publiés depuis une base Notion. Le calendrier éditorial reste là où l'équipe écrit déjà.",
            'en' => 'Seven networks — Instagram, LinkedIn, X, TikTok, Facebook, Threads, YouTube — published straight from a Notion database. The content calendar stays where the team already writes.',
        ],
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
        'tagline' => [
            'fr' => 'Résume vos messages vocaux WhatsApp en quelques secondes.',
            'en' => 'Summarises your WhatsApp voice notes in seconds.',
        ],
        'description' => [
            'fr' => 'Il suffit de transférer le vocal au contact Abrège pour recevoir un texte clair. Aucune application à installer, 5 résumés offerts chaque mois.',
            'en' => 'Forward the voice note to the Abrège contact and get clear text back. No app to install, 5 free summaries every month.',
        ],
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
        'tagline' => [
            'fr' => 'Les missions freelance publiées sur LinkedIn, par e-mail avant les autres.',
            'en' => 'Freelance gigs posted on LinkedIn, in your inbox before anyone else.',
        ],
        'description' => [
            'fr' => "Le flux est lu en continu, les offres salariées et les profils disponibles écartés. L'alerte part en temps réel ; l'abonnement débloque le nom du décideur et le lien vers sa publication.",
            'en' => "The feed is read continuously, salaried roles and available-for-hire posts filtered out. Alerts go out in real time; a subscription unlocks the decision-maker's name and the link to their post.",
        ],
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
