<?php

declare(strict_types=1);

namespace Pr4w\Ecosystem\Console;

use Illuminate\Console\Command;
use Pr4w\Ecosystem\Ecosystem;
use Pr4w\Ecosystem\Product;

class ListCommand extends Command
{
    protected $signature = 'ecosystem:list';

    protected $description = 'Affiche le catalogue de l’écosystème et ce que cette app affichera';

    public function handle(Ecosystem $ecosystem): int
    {
        $current = $ecosystem->current();

        $current
            ? $this->components->info("App courante : {$current->name} ({$current->key})")
            : $this->components->warn('App courante non détectée : aucune app ne sera masquée. Définis ECOSYSTEM_CURRENT.');

        $visible = $ecosystem->others();

        $this->table(
            ['Clé', 'Nom', 'URL', 'Logo', 'Statut'],
            $ecosystem->catalog()->map(fn (Product $product) => [
                $product->key,
                $product->name,
                $product->url,
                $this->formats($product),
                match (true) {
                    $ecosystem->isCurrent($product) => 'courante (masquée)',
                    ! $product->active => 'inactive',
                    $visible->has($product->key) => 'affichée',
                    default => 'exclue (config)',
                },
            ])->values()->all(),
        );

        $this->line('  <fg=gray>* format affiché par défaut (clé "prefer" du catalogue)</>');

        return self::SUCCESS;
    }

    /** Formats disponibles, le format retenu suivi d'une étoile. */
    private function formats(Product $product): string
    {
        $logo = $product->logo;
        $preferred = $logo->preferred();

        $available = array_filter([
            $logo->hasSvg() ? 'svg' : null,
            $logo->hasUrl() ? 'url' : null,
            'text',
        ]);

        return implode(' + ', array_map(
            fn (string $format) => $format === $preferred ? $format.'*' : $format,
            $available,
        ));
    }
}
