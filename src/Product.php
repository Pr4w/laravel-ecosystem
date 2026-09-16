<?php

declare(strict_types=1);

namespace Pr4w\Ecosystem;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Pr4w\Ecosystem\Exceptions\InvalidProductException;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class Product implements Arrayable, JsonSerializable
{
    /**
     * @param  list<string>  $tags
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public string $key,
        public string $name,
        public string $url,
        public string $tagline,
        public Logo $logo,
        public ?string $description = null,
        public ?string $color = null,
        public ?string $category = null,
        public array $tags = [],
        public array $meta = [],
        public bool $active = true,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  list<string>  $locales  Langues à essayer, dans l'ordre.
     */
    public static function fromArray(string $key, array $data, string $logosPath, array $locales = []): self
    {
        foreach (['name', 'url'] as $field) {
            if (! isset($data[$field]) || ! is_string($data[$field]) || trim($data[$field]) === '') {
                throw InvalidProductException::missingField($key, $field);
            }
        }

        $tagline = self::translate($data['tagline'] ?? null, $locales);

        if ($tagline === null) {
            throw InvalidProductException::missingField($key, 'tagline');
        }

        if (filter_var($data['url'], FILTER_VALIDATE_URL) === false) {
            throw InvalidProductException::invalidUrl($key, $data['url']);
        }

        return new self(
            key: $key,
            name: $data['name'],
            url: self::normalizeUrl($data['url']),
            tagline: $tagline,
            logo: Logo::make($data['logo'] ?? null, $logosPath, $data['name']),
            description: self::translate($data['description'] ?? null, $locales),
            color: $data['color'] ?? null,
            category: $data['category'] ?? null,
            tags: array_values($data['tags'] ?? []),
            meta: $data['meta'] ?? [],
            active: (bool) ($data['active'] ?? true),
        );
    }

    public function host(): string
    {
        return self::normalizeHost($this->url);
    }

    /**
     * Résout un champ traduisible.
     *
     * Une chaîne simple vaut pour toutes les langues. Un tableau
     * ['fr' => …, 'en' => …] est résolu dans l'ordre des langues
     * demandées, puis retombe sur la première valeur disponible :
     * un site n'affiche jamais un texte vide faute de traduction.
     *
     * @param  string|array<string, string>|null  $value
     * @param  list<string>  $locales
     */
    public static function translate(string|array|null $value, array $locales = []): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            return trim($value) === '' ? null : $value;
        }

        foreach ($locales as $locale) {
            foreach ([$locale, strtok((string) $locale, '_-')] as $candidate) {
                if (is_string($value[$candidate] ?? null) && trim($value[$candidate]) !== '') {
                    return $value[$candidate];
                }
            }
        }

        foreach ($value as $fallback) {
            if (is_string($fallback) && trim($fallback) !== '') {
                return $fallback;
            }
        }

        return null;
    }

    /**
     * Garantit un chemin : "https://x.app" devient "https://x.app/", pour que
     * les UTM donnent "https://x.app/?utm_…" et non "https://x.app?utm_…".
     * Un chemin existant est conservé tel quel.
     */
    public static function normalizeUrl(string $url): string
    {
        $parts = parse_url(trim($url)) ?: [];
        $path = $parts['path'] ?? '';

        return ($parts['scheme'] ?? 'https').'://'
            .($parts['host'] ?? '')
            .(isset($parts['port']) ? ':'.$parts['port'] : '')
            .($path === '' ? '/' : $path)
            .(isset($parts['query']) ? '?'.$parts['query'] : '')
            .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }

    public static function normalizeHost(?string $url): string
    {
        $host = strtolower((string) parse_url((string) $url, PHP_URL_HOST));

        return str_starts_with($host, 'www.') ? substr($host, 4) : $host;
    }

    /**
     * URL finale à utiliser dans le href (UTM inclus si activés).
     *
     * @param  array<string, scalar>  $query  paramètres additionnels (prioritaires)
     */
    public function link(array $query = []): string
    {
        $utm = (array) config('ecosystem.utm', []);

        if (filter_var($utm['enabled'] ?? false, FILTER_VALIDATE_BOOL)) {
            $source = $utm['source']
                ?? config('ecosystem.current')
                ?: self::normalizeHost(config('app.url'));

            $query += array_filter([
                'utm_source' => $source ?: null,
                'utm_medium' => $utm['medium'] ?? null,
                'utm_campaign' => $utm['campaign'] ?? null,
            ]);
        }

        if ($query === []) {
            return $this->url;
        }

        [$base, $fragment] = array_pad(explode('#', $this->url, 2), 2, null);

        $url = $base.(str_contains($base, '?') ? '&' : '?').http_build_query($query);

        return $fragment === null ? $url : $url.'#'.$fragment;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'url' => $this->url,
            'href' => $this->link(),
            'host' => $this->host(),
            'tagline' => $this->tagline,
            'description' => $this->description,
            'logo' => $this->logo->toArray(),
            'color' => $this->color,
            'category' => $this->category,
            'tags' => $this->tags,
            'meta' => $this->meta,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
