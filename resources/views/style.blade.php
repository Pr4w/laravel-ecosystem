{{--
    Styles des vues de l'écosystème.

    Volontairement autonomes : aucune dépendance à Tailwind ni à un build,
    pour que la vue s'affiche correctement dès l'installation.

    Pour l'adapter à ton site, redéfinis les variables sur .eco dans ta
    propre CSS, ou publie les vues et réécris-les :

        php artisan vendor:publish --tag=ecosystem-views
--}}
@once
    <style>
        .eco {
            --eco-gap: 1rem;
            --eco-mark: 1.5rem;
            --eco-muted: color-mix(in srgb, currentColor 60%, transparent);
            --eco-name-size: 0.8125rem;
            --eco-pitch-size: 0.6875rem;
        }

        .eco__heading {
            margin: 0 0 0.875rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: var(--eco-muted);
        }

        .eco__item {
            display: flex;
            align-items: flex-start;
            gap: 0.5625rem;
            color: inherit;
            text-decoration: none;
        }

        .eco__mark {
            flex: 0 0 auto;
            display: inline-grid;
            place-items: center;
            width: var(--eco-mark);
            height: var(--eco-mark);
            font-size: calc(var(--eco-mark) * 0.9);
            line-height: 1;
        }

        /* Un SVG ou une image occupe la boîte, un emoji est dimensionné par font-size. */
        .eco__mark > svg,
        .eco__mark > img {
            display: block;
            width: 100%;
            height: 100%;
        }

        .eco__text {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .eco__name {
            font-size: var(--eco-name-size);
            font-weight: 600;
        }

        .eco__pitch {
            font-size: var(--eco-pitch-size);
            line-height: 1.4;
            color: var(--eco-muted);
        }

        /* A — ligne discrète */
        .eco__row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem 0.5rem;
        }

        .eco__row .eco__item {
            align-items: center;
            gap: 0.375rem;
            font-size: var(--eco-name-size);
            font-weight: 500;
        }

        .eco__separator {
            color: var(--eco-muted);
            opacity: 0.5;
        }

        /* B — grille avec pitch */
        .eco__grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: var(--eco-gap);
        }

        /* E — deux colonnes */
        .eco__columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.8125rem 1.625rem;
        }

        @media (max-width: 768px) {
            .eco__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .eco__grid,
            .eco__columns {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
@endonce
