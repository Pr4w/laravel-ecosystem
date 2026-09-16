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
        'tagline' => 'Beta tagline',
        'logo' => 'https://beta.test/logo.png',
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
