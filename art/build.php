<?php

/*
|--------------------------------------------------------------------------
| Bande décorative du README
|--------------------------------------------------------------------------
| Régénère art/ecosystem.svg à partir du catalogue : les logos dans leur
| couleur de marque, les noms en gris neutre pour rester lisibles sur le
| thème clair comme sur le thème sombre de GitHub.
|
| À relancer après toute modification du catalogue, depuis la racine :
|
|     php art/build.php
*/

if (! is_file('resources/products.php')) {
    fwrite(STDERR, "À lancer depuis la racine du package.\n");
    exit(1);
}

$catalog = require 'resources/products.php';

$cells = [];
foreach ($catalog as $p) {
    $logo = $p['logo'] ?? [];
    $usesEmoji = ($logo['prefer'] ?? null) === 'text';

    $cells[] = [
        'name' => $p['name'],
        'color' => $p['color'] ?? '#71717a',
        'svg' => (! $usesEmoji && isset($logo['svg']))
            ? trim(file_get_contents('resources/logos/'.$logo['svg']))
            : null,
        'emoji' => $usesEmoji ? ($logo['text'] ?? null) : null,
    ];
}

$cellW = 172;
$width = $cellW * count($cells);
$height = 86;
$mark = 30;
$font = '-apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif';

// Gris neutre : lisible sur le thème clair comme sur le thème sombre de GitHub,
// alors qu'une couleur de marque par nom donnerait quatre teintes discordantes.
$nameColor = '#71717a';

$parts = [];

foreach ($cells as $i => $c) {
    $cx = $i * $cellW + $cellW / 2;

    if ($c['svg'] !== null) {
        // Le SVG du catalogue est réutilisé tel quel, teinté par la couleur de marque
        // via l'attribut "color", que currentColor résout.
        preg_match('/viewBox="([^"]+)"/', $c['svg'], $vb);

        $attrs = [];
        foreach (['fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin'] as $a) {
            if (preg_match('/\s'.$a.'="([^"]+)"/', $c['svg'], $m)) {
                $attrs[] = $a.'="'.$m[1].'"';
            }
        }

        $inner = preg_replace('/^<svg[^>]*>/s', '', $c['svg']);
        $inner = preg_replace('#</svg>\s*$#', '', $inner);

        $parts[] = sprintf(
            '<svg x="%s" y="13" width="%d" height="%d" viewBox="%s" color="%s" %s>%s</svg>',
            round($cx - $mark / 2, 1), $mark, $mark, $vb[1], $c['color'], implode(' ', $attrs), $inner
        );
    } else {
        $parts[] = sprintf(
            '<text x="%s" y="38" text-anchor="middle" font-size="26">%s</text>',
            round($cx, 1), $c['emoji']
        );
    }

    $parts[] = sprintf(
        '<text x="%s" y="66" text-anchor="middle" font-family="%s" font-size="13" font-weight="600" fill="%s">%s</text>',
        round($cx, 1), $font, $nameColor, htmlspecialchars($c['name'], ENT_XML1)
    );
}

$svg = sprintf(
    '<svg xmlns="http://www.w3.org/2000/svg" width="%d" height="%d" viewBox="0 0 %d %d" role="img" aria-label="%s">%s</svg>',
    $width, $height, $width, $height,
    htmlspecialchars(implode(' · ', array_column($cells, 'name')), ENT_XML1),
    implode('', $parts)
);

file_put_contents('art/ecosystem.svg', $svg."\n");
echo 'écrit : '.strlen($svg)." octets\n";
