<?php

declare(strict_types=1);

namespace Pr4w\Ecosystem;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Collection;
use Pr4w\Ecosystem\Exceptions\InvalidProductException;

class Ecosystem
{
    /** @var array<string, Collection<string, Product>> Catalogue par langue. */
    private array $catalogs = [];

    public function __construct(private readonly Repository $config) {}

    /**
     * Catalogue brut : tous les produits, y compris inactifs et app courante.
     *
     * @return Collection<string, Product>
     */
    public function catalog(): Collection
    {
        $locales = $this->locales();

        return $this->catalogs[implode('|', $locales)] ??= $this->load($locales);
    }

    /**
     * Langues à essayer pour les textes du catalogue, dans l'ordre :
     * celle de la requête, puis celle de repli de l'app.
     *
     * @return list<string>
     */
    public function locales(): array
    {
        return array_values(array_unique(array_filter([
            (string) ($this->config->get('ecosystem.locale') ?: $this->config->get('app.locale')),
            (string) ($this->config->get('ecosystem.fallback_locale') ?: $this->config->get('app.fallback_locale')),
        ])));
    }

    /**
     * Produits affichables (actifs, hors exclusions), app courante incluse.
     *
     * @return Collection<string, Product>
     */
    public function all(): Collection
    {
        $except = (array) $this->config->get('ecosystem.except', []);

        return $this->catalog()
            ->filter(fn (Product $product) => $product->active)
            ->reject(fn (Product $product) => in_array($product->key, $except, true));
    }

    /**
     * Ce que le site doit afficher : les autres apps.
     *
     * @return Collection<string, Product>
     */
    public function others(?int $limit = null): Collection
    {
        $others = $this->all()->reject(fn (Product $product) => $this->isCurrent($product));

        return $limit !== null ? $others->take($limit) : $others;
    }

    public function current(): ?Product
    {
        return $this->catalog()->first(fn (Product $product) => $this->isCurrent($product));
    }

    public function find(string $key): ?Product
    {
        return $this->catalog()->get($key);
    }

    public function has(string $key): bool
    {
        return $this->catalog()->has($key);
    }

    public function isCurrent(Product $product): bool
    {
        $current = $this->config->get('ecosystem.current');

        if (is_string($current) && $current !== '') {
            return $product->key === $current;
        }

        $appHost = Product::normalizeHost($this->config->get('app.url'));

        return $appHost !== '' && $product->host() === $appHost;
    }

    /**
     * Tableau prêt pour Inertia / JSON / API.
     *
     * @return list<array<string, mixed>>
     */
    public function toArray(?int $limit = null): array
    {
        return $this->others($limit)
            ->values()
            ->map(fn (Product $product) => $product->toArray())
            ->all();
    }

    /** Vide le cache mémoire (tests, changement de config à chaud). */
    public function flush(): void
    {
        $this->catalogs = [];
    }

    public static function defaultCatalogPath(): string
    {
        return dirname(__DIR__).'/resources/products.php';
    }

    /**
     * @param  list<string>  $locales
     * @return Collection<string, Product>
     */
    private function load(array $locales): Collection
    {
        $path = $this->config->get('ecosystem.catalog') ?: self::defaultCatalogPath();

        if (! is_file($path)) {
            throw InvalidProductException::missingCatalog($path);
        }

        $data = require $path;

        if (! is_array($data)) {
            throw InvalidProductException::invalidCatalog($path);
        }

        $logosPath = $this->config->get('ecosystem.logos_path') ?: dirname($path).'/logos';

        return collect($data)->mapWithKeys(fn (mixed $item, int|string $key) => [
            (string) $key => Product::fromArray((string) $key, (array) $item, $logosPath, $locales),
        ]);
    }
}
