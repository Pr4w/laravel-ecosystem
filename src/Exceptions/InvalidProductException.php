<?php

declare(strict_types=1);

namespace Pr4w\Ecosystem\Exceptions;

use InvalidArgumentException;

final class InvalidProductException extends InvalidArgumentException
{
    public static function missingCatalog(string $path): self
    {
        return new self("Catalogue introuvable : {$path}");
    }

    public static function invalidCatalog(string $path): self
    {
        return new self("Le catalogue {$path} doit retourner un tableau.");
    }

    public static function missingField(string $key, string $field): self
    {
        return new self("Produit [{$key}] : le champ \"{$field}\" est obligatoire.");
    }

    public static function invalidUrl(string $key, string $url): self
    {
        return new self("Produit [{$key}] : URL invalide \"{$url}\".");
    }

    public static function unknownLogoKey(string $product, string $key): self
    {
        return new self(
            "Produit [{$product}] : clé de logo inconnue \"{$key}\". Clés acceptées : svg, url, text, alt, prefer."
        );
    }

    public static function invalidPreference(string $product, string $value): self
    {
        return new self(
            "Produit [{$product}] : préférence de logo invalide \"{$value}\". Valeurs acceptées : svg, url, text."
        );
    }

    public static function missingSvg(string $product, string $path): self
    {
        return new self("Produit [{$product}] : fichier SVG introuvable ({$path}).");
    }

    public static function invalidSvg(string $product, string $path): self
    {
        return new self("Produit [{$product}] : le fichier {$path} n'est pas un SVG valide.");
    }
}
