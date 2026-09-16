<?php

use Pr4w\Ecosystem\Facades\Ecosystem;

beforeEach(function () {
    config()->set('ecosystem.catalog', __DIR__.'/Fixtures/products.php');
    config()->set('ecosystem.current', 'alpha');
    config()->set('app.locale', 'fr');
    config()->set('app.fallback_locale', 'en');
    config()->set('ecosystem.heading', ['fr' => 'Nos autres outils', 'en' => 'Our other tools']);
    Ecosystem::flush();
});

it('rend les trois dispositions', function (string $component, string $wrapper) {
    $html = $this->blade("<x-ecosystem::{$component} />");

    $html->assertSee('eco--'.$component, false)
        ->assertSee($wrapper, false)
        ->assertSee('Nos autres outils')
        ->assertSee('Beta App')
        ->assertSee('https://beta.test/?utm_content=footer', false)
        ->assertSee('utm_source=alpha', false)
        ->assertDontSee('Alpha'); // l'app courante ne s'affiche pas
})->with([
    ['inline', 'eco__row'],
    ['grid', 'eco__grid'],
    ['columns', 'eco__columns'],
]);

it('affiche le pitch dans grid et columns, pas dans inline', function () {
    expect((string) $this->blade('<x-ecosystem::grid />'))->toContain('Pitch de Beta')
        ->and((string) $this->blade('<x-ecosystem::columns />'))->toContain('Pitch de Beta')
        ->and((string) $this->blade('<x-ecosystem::inline />'))->not->toContain('Pitch de Beta');
});

it('traduit le libellé commun', function () {
    app()->setLocale('en');

    $this->blade('<x-ecosystem::grid />')->assertSee('Our other tools');
});

it('accepte un libellé passé en attribut, et pas de libellé du tout', function () {
    $this->blade('<x-ecosystem::grid heading="Du même atelier" />')->assertSee('Du même atelier');

    config()->set('ecosystem.heading', null);

    expect((string) $this->blade('<x-ecosystem::grid />'))->not->toContain('<p class="eco__heading">');
});

it('ajoute utm_content pour distinguer les emplacements', function () {
    expect((string) $this->blade('<x-ecosystem::inline />'))->toContain('utm_content=footer')
        ->and((string) $this->blade('<x-ecosystem::inline utm-content="sidebar" />'))->toContain('utm_content=sidebar');
});

it('teinte le visuel avec la couleur du produit', function () {
    expect((string) $this->blade('<x-ecosystem::grid />'))
        ->toContain('style="color: #123456"');
});

it('respecte la limite', function () {
    config()->set('ecosystem.current', null);
    config()->set('app.url', 'https://nowhere.test');
    Ecosystem::flush();

    expect((string) $this->blade('<x-ecosystem::inline :limit="1" />'))->toContain('Alpha')
        ->not->toContain('Beta App');
});

it('n’écrit rien quand il n’y a aucune autre app', function () {
    config()->set('ecosystem.except', ['beta', 'delta']);
    Ecosystem::flush();

    expect(trim((string) $this->blade('<x-ecosystem::grid />')))->toBe('');
});

it('n’inclut la feuille de style qu’une fois par page', function () {
    $html = (string) $this->blade('<x-ecosystem::inline /><x-ecosystem::grid />');

    expect(substr_count($html, '<style>'))->toBe(1);
});

it('laisse le site ajouter ses propres classes', function () {
    $this->blade('<x-ecosystem::grid class="mt-10" />')->assertSee('eco eco--grid mt-10', false);
});
