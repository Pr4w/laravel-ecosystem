<?php

declare(strict_types=1);

namespace Pr4w\Ecosystem\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection<string, \Pr4w\Ecosystem\Product> catalog()
 * @method static \Illuminate\Support\Collection<string, \Pr4w\Ecosystem\Product> all()
 * @method static \Illuminate\Support\Collection<string, \Pr4w\Ecosystem\Product> others(?int $limit = null)
 * @method static \Pr4w\Ecosystem\Product|null current()
 * @method static \Pr4w\Ecosystem\Product|null find(string $key)
 * @method static bool has(string $key)
 * @method static bool isCurrent(\Pr4w\Ecosystem\Product $product)
 * @method static list<array<string, mixed>> toArray(?int $limit = null)
 * @method static void flush()
 *
 * @see \Pr4w\Ecosystem\Ecosystem
 */
class Ecosystem extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Pr4w\Ecosystem\Ecosystem::class;
    }
}
