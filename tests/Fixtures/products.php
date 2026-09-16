<?php

return [
    'alpha' => [
        'name' => 'Alpha',
        'url' => 'https://alpha.test',
        'tagline' => 'Alpha tagline',
        'logo' => 'alpha.svg',
    ],
    'beta' => [
        'name' => 'Beta App',
        'url' => 'https://beta.test/',
        'tagline' => ['fr' => 'Pitch de Beta', 'en' => 'Beta tagline'],
        'description' => ['en' => 'Only English'],
        'logo' => 'https://beta.test/logo.png',
        'color' => '#123456',
    ],
    'delta' => [
        'name' => 'Delta',
        'url' => 'https://delta.test',
        'tagline' => 'Delta tagline',
        'logo' => [
            'text' => '🍿',
            'prefer' => 'text',
            'svg' => 'alpha.svg',
            'url' => 'https://delta.test/logo.png',
        ],
    ],
    'gamma' => [
        'name' => 'Gamma',
        'url' => 'https://gamma.test',
        'tagline' => 'Gamma tagline',
        'active' => false,
    ],
];
