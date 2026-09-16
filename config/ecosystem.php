<?php

return [

    /*
    |--------------------------------------------------------------------------
    | App courante
    |--------------------------------------------------------------------------
    | Clé de cette app dans le catalogue (ex. "laredac"). Elle sera masquée
    | des liens affichés. Si vide, détection automatique via APP_URL.
    */
    'current' => env('ECOSYSTEM_CURRENT'),

    /*
    |--------------------------------------------------------------------------
    | Exclusions
    |--------------------------------------------------------------------------
    | Clés à ne jamais afficher sur ce site.
    */
    'except' => [],

    /*
    |--------------------------------------------------------------------------
    | UTM
    |--------------------------------------------------------------------------
    | Ajoutés au lien "href". "source" vaut par défaut la clé de l'app
    | courante, sinon le host de APP_URL.
    */
    'utm' => [
        'enabled' => env('ECOSYSTEM_UTM', true),
        'source' => null,
        'medium' => 'ecosystem',
        'campaign' => 'cross-promo',
    ],

    /*
    |--------------------------------------------------------------------------
    | Langues
    |--------------------------------------------------------------------------
    | Les champs "tagline" et "description" du catalogue acceptent soit une
    | chaîne, soit un tableau ['fr' => …, 'en' => …]. La langue de la requête
    | est utilisée, puis celle de repli. Laisser à null pour suivre l'app.
    */
    'locale' => null,
    'fallback_locale' => null,

    /*
    |--------------------------------------------------------------------------
    | Surcharges (tests / preview uniquement)
    |--------------------------------------------------------------------------
    | Laisser à null en production : le catalogue du package fait foi.
    */
    'catalog' => null,
    'logos_path' => null,

];
