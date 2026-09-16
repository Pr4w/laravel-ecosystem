<?php

declare(strict_types=1);

namespace Pr4w\Ecosystem;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use JsonSerializable;
use Pr4w\Ecosystem\Exceptions\InvalidProductException;

/**
 * Logo multi-format : chaque site choisit le rendu qui lui convient.
 *
 * @implements Arrayable<string, string|null>
 */
final readonly class Logo implements Arrayable, Htmlable, JsonSerializable
{
    public const SVG = 'svg';

    public const URL = 'url';

    public const TEXT = 'text';

    public const DEFAULT_ORDER = [self::SVG, self::URL, self::TEXT];

    public function __construct(
        public ?string $svg = null,
        public ?string $url = null,
        public ?string $text = null,
        public ?string $alt = null,
    ) {}

    /**
     * @param  string|array{svg?: string, url?: string, text?: string, alt?: string}|null  $definition
     */
    public static function make(string|array|null $definition, string $logosPath, string $productName): self
    {
        if (is_string($definition)) {
            $definition = str_starts_with($definition, 'http://') || str_starts_with($definition, 'https://')
                ? ['url' => $definition]
                : ['svg' => $definition];
        }

        $definition ??= [];

        return new self(
            svg: isset($definition['svg']) ? self::loadSvg($definition['svg'], $logosPath, $productName) : null,
            url: $definition['url'] ?? null,
            text: $definition['text'] ?? self::initials($productName),
            alt: $definition['alt'] ?? $productName,
        );
    }

    public function hasSvg(): bool
    {
        return $this->svg !== null && $this->svg !== '';
    }

    public function hasUrl(): bool
    {
        return $this->url !== null && $this->url !== '';
    }

    /**
     * Premier format disponible selon l'ordre de préférence.
     *
     * @param  list<string>  $order
     */
    public function preferred(array $order = self::DEFAULT_ORDER): string
    {
        foreach ($order as $type) {
            $available = match ($type) {
                self::SVG => $this->hasSvg(),
                self::URL => $this->hasUrl(),
                self::TEXT => $this->text !== null && $this->text !== '',
                default => false,
            };

            if ($available) {
                return $type;
            }
        }

        return self::TEXT;
    }

    /** SVG inline, avec classes CSS optionnelles fusionnées sur la balise <svg>. */
    public function svg(?string $class = null): ?HtmlString
    {
        if (! $this->hasSvg()) {
            return null;
        }

        $svg = (string) $this->svg;

        if ($class !== null && $class !== '') {
            $escaped = e($class);

            $svg = preg_match('/<svg\b[^>]*\sclass="/i', $svg)
                ? (string) preg_replace('/(<svg\b[^>]*\sclass=")/i', '${1}'.$escaped.' ', $svg, 1)
                : (string) preg_replace('/<svg\b/i', '<svg class="'.$escaped.'"', $svg, 1);
        }

        return new HtmlString($svg);
    }

    /** Balise <img> pointant vers l'URL du logo. */
    public function img(?string $class = null): ?HtmlString
    {
        if (! $this->hasUrl()) {
            return null;
        }

        return new HtmlString(sprintf(
            '<img src="%s" alt="%s"%s loading="lazy" decoding="async">',
            e((string) $this->url),
            e((string) $this->alt),
            $class ? ' class="'.e($class).'"' : '',
        ));
    }

    /**
     * Rendu HTML automatique selon l'ordre de préférence.
     *
     * @param  list<string>  $order
     */
    public function render(?string $class = null, array $order = self::DEFAULT_ORDER): HtmlString
    {
        return match ($this->preferred($order)) {
            self::SVG => $this->svg($class) ?? new HtmlString(''),
            self::URL => $this->img($class) ?? new HtmlString(''),
            default => new HtmlString(sprintf(
                '<span%s aria-hidden="true">%s</span>',
                $class ? ' class="'.e($class).'"' : '',
                e((string) $this->text),
            )),
        };
    }

    public function toHtml(): string
    {
        return $this->render()->toHtml();
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }

    /** @return array{svg: string|null, url: string|null, text: string|null, alt: string|null, preferred: string} */
    public function toArray(): array
    {
        return [
            'svg' => $this->svg,
            'url' => $this->url,
            'text' => $this->text,
            'alt' => $this->alt,
            'preferred' => $this->preferred(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private static function loadSvg(string $svg, string $logosPath, string $productName): string
    {
        // SVG brut directement dans le catalogue
        if (str_starts_with(ltrim($svg), '<svg')) {
            return trim($svg);
        }

        $path = rtrim($logosPath, '/').'/'.ltrim($svg, '/');

        if (! is_file($path)) {
            throw InvalidProductException::missingSvg($productName, $path);
        }

        $content = trim((string) file_get_contents($path));

        // Retire prologue XML, DOCTYPE et commentaires pour pouvoir l'inliner
        $content = trim((string) preg_replace(
            ['/<\?xml[^>]*\?>/i', '/<!DOCTYPE[^>]*>/i', '/<!--.*?-->/s'],
            '',
            $content,
        ));

        if (! str_starts_with($content, '<svg')) {
            throw InvalidProductException::invalidSvg($productName, $path);
        }

        return $content;
    }

    private static function initials(string $name): string
    {
        $clean = (string) preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $name);
        $words = array_filter(preg_split('/[\s\-_]+/u', trim($clean)) ?: []);

        $initials = implode('', array_map(
            fn (string $word) => mb_strtoupper(mb_substr($word, 0, 1)),
            array_slice(array_values($words), 0, 2),
        ));

        return $initials !== '' ? $initials : mb_strtoupper(mb_substr($name, 0, 1));
    }
}
