<?php

use Pr4w\Ecosystem\Exceptions\InvalidProductException;
use Pr4w\Ecosystem\Facades\Ecosystem;
use Pr4w\Ecosystem\Logo;
use Pr4w\Ecosystem\Product;

beforeEach(function () {
    config()->set('ecosystem.catalog', __DIR__.'/Fixtures/products.php');
    config()->set('ecosystem.current', null);
    config()->set('app.url', 'https://www.alpha.test');
    config()->set('app.locale', 'fr');
    config()->set('app.fallback_locale', 'en');
    Ecosystem::flush();
});

it('masque l’app courante détectée via APP_URL et les produits inactifs', function () {
    expect(Ecosystem::others()->keys()->all())->toBe(['beta', 'delta'])
        ->and(Ecosystem::current()?->key)->toBe('alpha');
});

it('priorise ECOSYSTEM_CURRENT sur APP_URL', function () {
    config()->set('ecosystem.current', 'beta');

    expect(Ecosystem::others()->keys()->all())->toBe(['alpha', 'delta'])
        ->and(Ecosystem::current()?->key)->toBe('beta');
});

it('respecte les exclusions', function () {
    config()->set('ecosystem.except', ['beta', 'delta']);

    expect(Ecosystem::others())->toBeEmpty();
});

it('affiche l’emoji quand le catalogue déclare prefer = text', function () {
    $logo = Ecosystem::find('delta')->logo;

    expect($logo->prefer)->toBe('text')
        ->and($logo->hasSvg())->toBeTrue()
        ->and($logo->preferred())->toBe('text')
        ->and((string) $logo->render('text-2xl'))->toBe('<span class="text-2xl" aria-hidden="true">🍿</span>')
        ->and($logo->toArray()['preferred'])->toBe('text');
});

it('laisse le site passer outre la préférence du catalogue', function () {
    $logo = Ecosystem::find('delta')->logo;

    expect($logo->preferred([Logo::SVG, Logo::TEXT]))->toBe('svg')
        ->and((string) $logo->render('size-6', [Logo::SVG]))->toContain('<svg');
});

it('rejette une clé de logo inconnue', function () {
    Product::fromArray('x', [
        'name' => 'X', 'url' => 'https://x.test', 'tagline' => 'x',
        'logo' => ['text' => 'X', 'preferred' => 'text'],
    ], '/tmp');
})->throws(InvalidProductException::class, 'clé de logo inconnue');

it('rejette une préférence de logo invalide', function () {
    Product::fromArray('x', [
        'name' => 'X', 'url' => 'https://x.test', 'tagline' => 'x',
        'logo' => ['text' => 'X', 'prefer' => 'emoji'],
    ], '/tmp');
})->throws(InvalidProductException::class, 'préférence de logo invalide');

it('rend les textes dans la langue de l’app', function () {
    expect(Ecosystem::find('beta')->tagline)->toBe('Pitch de Beta');

    app()->setLocale('en');

    expect(Ecosystem::find('beta')->tagline)->toBe('Beta tagline');
});

it('change de langue sans vider le cache', function () {
    expect(Ecosystem::find('beta')->tagline)->toBe('Pitch de Beta');

    app()->setLocale('en');

    expect(Ecosystem::find('beta')->tagline)->toBe('Beta tagline');

    app()->setLocale('fr');

    expect(Ecosystem::find('beta')->tagline)->toBe('Pitch de Beta');
});

it('ramène une locale régionale à sa langue de base', function () {
    app()->setLocale('fr_CA');

    expect(Ecosystem::find('beta')->tagline)->toBe('Pitch de Beta');
});

it('retombe sur la langue de repli puis sur la première disponible', function () {
    app()->setLocale('es');

    expect(Ecosystem::find('beta')->tagline)->toBe('Beta tagline')
        ->and(Ecosystem::find('beta')->description)->toBe('Only English');

    config()->set('app.fallback_locale', 'de');
    Ecosystem::flush();

    expect(Ecosystem::find('beta')->tagline)->toBe('Pitch de Beta');
});

it('accepte encore une chaîne simple pour toutes les langues', function () {
    app()->setLocale('en');

    expect(Ecosystem::find('alpha')->tagline)->toBe('Alpha tagline');
});

it('rejette un produit dont la tagline n’existe dans aucune langue', function () {
    Product::fromArray('x', [
        'name' => 'X', 'url' => 'https://x.test', 'tagline' => ['fr' => '  '],
    ], '/tmp', ['fr']);
})->throws(InvalidProductException::class, 'tagline');

it('ajoute les UTM au lien', function () {
    config()->set('ecosystem.current', 'alpha');

    expect(Ecosystem::find('beta')->link())
        ->toBe('https://beta.test/?utm_source=alpha&utm_medium=ecosystem&utm_campaign=cross-promo');
});

it('utilise le host comme source UTM par défaut', function () {
    expect(Ecosystem::find('beta')->link())->toContain('utm_source=alpha.test');
});

it('peut désactiver les UTM', function () {
    config()->set('ecosystem.utm.enabled', false);

    expect(Ecosystem::find('beta')->link())->toBe('https://beta.test/');
});

it('ajoute un slash final aux URL racine et conserve les chemins', function () {
    expect(Ecosystem::find('alpha')->url)->toBe('https://alpha.test/')
        ->and(Ecosystem::find('beta')->url)->toBe('https://beta.test/')
        ->and(Product::normalizeUrl('https://x.test/fr'))->toBe('https://x.test/fr')
        ->and(Product::normalizeUrl('https://x.test:8443?ref=1#top'))->toBe('https://x.test:8443/?ref=1#top');
});

it('insère les UTM avant le fragment et après une query existante', function () {
    config()->set('ecosystem.current', 'alpha');

    $product = Product::fromArray('x', [
        'name' => 'X', 'url' => 'https://x.test?ref=1#top', 'tagline' => 'x',
    ], '/tmp');

    expect($product->link())
        ->toBe('https://x.test/?ref=1&utm_source=alpha&utm_medium=ecosystem&utm_campaign=cross-promo#top');
});

it('gère un logo SVG local', function () {
    $logo = Ecosystem::find('alpha')->logo;

    expect($logo->hasSvg())->toBeTrue()
        ->and($logo->preferred())->toBe('svg')
        ->and($logo->svg)->not->toContain('<?xml')
        ->and((string) $logo->svg('h-6 w-6'))->toContain('class="h-6 w-6 logo"');
});

it('gère un logo URL et génère un texte de secours', function () {
    $logo = Ecosystem::find('beta')->logo;

    expect($logo->preferred())->toBe('url')
        ->and($logo->text)->toBe('BA')
        ->and((string) $logo->render('size-6'))->toContain('<img src="https://beta.test/logo.png"');
});

it('retombe sur le texte sans SVG ni URL', function () {
    $logo = Ecosystem::find('gamma')->logo;

    expect($logo->preferred())->toBe('text')
        ->and((string) $logo)->toBe('<span aria-hidden="true">G</span>');
});

it('respecte un ordre de préférence personnalisé', function () {
    expect(Ecosystem::find('beta')->logo->preferred(['text', 'url']))->toBe('text');
});

it('sérialise pour Inertia / JSON', function () {
    $array = Ecosystem::toArray();

    expect($array)->toHaveCount(2)
        ->and($array[0])->toHaveKeys(['key', 'name', 'href', 'tagline', 'logo'])
        ->and($array[0]['logo'])->toHaveKeys(['svg', 'url', 'text', 'alt', 'preferred'])
        ->and(json_encode(Ecosystem::find('beta')))->toBeJson();
});

it('rejette un produit incomplet', function () {
    Product::fromArray('x', ['name' => 'X'], '/tmp');
})->throws(InvalidProductException::class);

it('rejette un SVG manquant', function () {
    Product::fromArray('x', [
        'name' => 'X', 'url' => 'https://x.test', 'tagline' => 'x', 'logo' => 'absent.svg',
    ], __DIR__.'/Fixtures/logos');
})->throws(InvalidProductException::class);

it('liste le catalogue en console', function () {
    $this->artisan('ecosystem:list')->assertSuccessful();
});
