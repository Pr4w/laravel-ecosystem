<?php

use Pr4w\Ecosystem\Facades\Ecosystem;
use Pr4w\Ecosystem\Logo;
use Pr4w\Ecosystem\Product;

/*
 * Garde-fou du vrai catalogue livré : à faire passer avant chaque tag.
 */

beforeEach(function () {
    config()->set('ecosystem.catalog', null);
    config()->set('app.locale', 'fr');
    config()->set('app.fallback_locale', 'fr');
    Ecosystem::flush();
});

it('charge le catalogue livré sans erreur', function () {
    expect(Ecosystem::catalog())->not->toBeEmpty();
});

it('est traduit en français et en anglais', function () {
    foreach (['fr', 'en'] as $locale) {
        app()->setLocale($locale);
        Ecosystem::flush();

        Ecosystem::all()->each(function (Product $product) use ($locale) {
            expect(trim($product->tagline))->not->toBe('', "{$product->key} n'a pas de tagline en {$locale}")
                ->and(trim((string) $product->description))->not->toBe('', "{$product->key} n'a pas de description en {$locale}");
        });
    }
});

it('a des clés au bon format', function () {
    Ecosystem::catalog()->each(
        fn (Product $product) => expect($product->key)->toMatch('/^[a-z0-9-]+$/')
    );
});

it('n’a pas deux produits sur le même domaine', function () {
    expect(Ecosystem::catalog()->map(fn (Product $p) => $p->host())->duplicates())->toBeEmpty();
});

it('a un visuel pour chaque produit actif', function () {
    Ecosystem::all()->each(function (Product $product) {
        $logo = $product->logo;

        expect($logo->hasSvg() || $logo->hasUrl() || $logo->prefer === Logo::TEXT)
            ->toBeTrue("{$product->key} n'a ni SVG, ni URL, ni emoji assumé (prefer => 'text')");
    });
});

it('affiche le format déclaré par chaque produit', function () {
    Ecosystem::all()->each(function (Product $product) {
        $logo = $product->logo;

        if ($logo->prefer !== null) {
            expect($logo->preferred())->toBe($logo->prefer, "{$product->key} ne rend pas le format déclaré");
        }
    });
});
