# pr4w/laravel-ecosystem

Catalogue partagé de toutes les apps pr4w (LaRédac', NotionScheduler, Abrège…).
Chaque app installe le package et récupère **la liste des autres apps** (nom, URL, pitch, logo, couleur…) pour afficher des liens croisés **avec son propre design**.

Le package ne fournit **aucune vue** : il fournit des données propres. Chaque site fait son rendu.

- Laravel 12 et 13, PHP 8.2+
- Zéro requête réseau, zéro base de données : le catalogue est un fichier PHP versionné dans le package
- Rendu serveur possible (Blade) ou via props (Inertia / Vue), donc les liens sont crawlables

---

## Sommaire

1. [Installation](#installation)
2. [Configuration de l'app](#configuration-de-lapp)
3. [Afficher les liens](#afficher-les-liens) (Blade, Inertia + Vue, JSON)
4. [Les logos](#les-logos)
5. [Référence de l'API](#référence-de-lapi)
6. [Générer la fiche d'une app avec Claude Code](#générer-la-fiche-dune-app-avec-claude-code) (prompts)
7. [Ajouter ou modifier une app (mainteneur)](#ajouter-ou-modifier-une-app-mainteneur)
8. [Tests](#tests)

---

## Installation

Le package est privé. Dans le `composer.json` de l'app, déclare le dépôt :

```json
"repositories": [
    { "type": "vcs", "url": "git@github.com:pr4w/laravel-ecosystem.git" }
]
```

Puis :

```bash
composer require pr4w/laravel-ecosystem
```

Le service provider et la façade `Ecosystem` sont enregistrés automatiquement.

## Configuration de l'app

Dans le `.env`, indique quelle app du catalogue est **celle-ci**, pour qu'elle ne s'affiche pas elle-même :

```dotenv
ECOSYSTEM_CURRENT=laredac
```

Sans cette variable, le package compare le domaine de `APP_URL` à celui de chaque produit (`www.` ignoré). Ça marche en prod, mais pas en local (`laredac.test`) : **définis toujours `ECOSYSTEM_CURRENT`.**

Vérifie le résultat :

```bash
php artisan ecosystem:list
```

```
 INFO  App courante : LaRédac' (laredac)

+-----------------+-----------------+-----------------------------+-----------------+--------------------+
| Clé             | Nom             | URL                         | Logo            | Statut             |
+-----------------+-----------------+-----------------------------+-----------------+--------------------+
| laredac         | LaRédac'        | https://laredac.ai/          | svg + url + text| courante (masquée) |
| notionscheduler | NotionScheduler | https://notionscheduler.app/ | svg + text      | affichée           |
| abrege          | Abrège          | https://abrege.app/          | svg + text      | affichée           |
| mission-monitor | Mission Monitor | https://…                    | text            | inactive           |
+-----------------+-----------------+-----------------------------+-----------------+--------------------+
```

### Options (facultatif)

```bash
php artisan vendor:publish --tag=ecosystem-config
```

| Clé | Défaut | Rôle |
|---|---|---|
| `current` | `env('ECOSYSTEM_CURRENT')` | Clé de l'app courante |
| `except` | `[]` | Clés à ne jamais afficher sur ce site |
| `utm.enabled` | `env('ECOSYSTEM_UTM', true)` | Ajoute des UTM au `href` |
| `utm.source` | `null` | Par défaut : clé courante, sinon host de `APP_URL` |
| `utm.medium` | `ecosystem` | |
| `utm.campaign` | `cross-promo` | |
| `catalog` / `logos_path` | `null` | Surcharges pour tests uniquement |

## Afficher les liens

La méthode à retenir : **`Ecosystem::others()`**. Elle renvoie les apps actives, hors app courante, dans l'ordre du catalogue.

> Utilise toujours `$app->link()` (ou `href` côté tableau) dans les liens, et `$app->url` uniquement pour l'affichage : `link()` ajoute les UTM, ce qui te permet de voir dans l'analytics quelle app envoie du trafic.

### Blade

```blade
@use('Pr4w\Ecosystem\Facades\Ecosystem')

<footer>
    <p>Autres outils par Mark</p>

    <ul class="grid gap-4 sm:grid-cols-3">
        @foreach (Ecosystem::others() as $app)
            <li>
                <a href="{{ $app->link() }}" class="flex items-center gap-3">
                    {{ $app->logo->render('size-8 text-zinc-500') }}
                    <span>
                        <strong>{{ $app->name }}</strong>
                        <small>{{ $app->tagline }}</small>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
</footer>
```

`render()` choisit automatiquement le meilleur format disponible (SVG → image → texte). Tu peux aussi le forcer, voir [Les logos](#les-logos).

### Inertia + Vue

Partage les données une fois dans `HandleInertiaRequests` :

```php
use Pr4w\Ecosystem\Facades\Ecosystem;

public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'ecosystem' => fn () => Ecosystem::toArray(),
    ];
}
```

Pour ne l'envoyer que sur certaines pages, utilise plutôt `Inertia::optional(fn () => Ecosystem::toArray())` ou passe-le en prop de la page concernée.

Composant Vue :

```vue
<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const apps = computed(() => usePage().props.ecosystem ?? [])
</script>

<template>
  <ul class="grid gap-4 sm:grid-cols-3">
    <li v-for="app in apps" :key="app.key">
      <a :href="app.href" class="flex items-center gap-3">
        <span
          v-if="app.logo.preferred === 'svg'"
          class="size-8 [&>svg]:size-full"
          v-html="app.logo.svg"
        />
        <img
          v-else-if="app.logo.preferred === 'url'"
          :src="app.logo.url"
          :alt="app.logo.alt"
          class="size-8"
          loading="lazy"
        />
        <span v-else class="size-8 grid place-items-center">{{ app.logo.text }}</span>

        <span>
          <strong>{{ app.name }}</strong>
          <small>{{ app.tagline }}</small>
        </span>
      </a>
    </li>
  </ul>
</template>
```

`v-html` est sûr ici : les SVG viennent du package, pas d'une saisie utilisateur.

### Format du tableau

`Ecosystem::toArray()` renvoie une liste :

```php
[
    [
        'key' => 'notionscheduler',
        'name' => 'NotionScheduler',
        'url' => 'https://notionscheduler.app/',
        'href' => 'https://notionscheduler.app/?utm_source=laredac&utm_medium=ecosystem&utm_campaign=cross-promo',
        'host' => 'notionscheduler.app',
        'tagline' => 'Planifiez vos posts réseaux sociaux depuis Notion.',
        'description' => null,
        'logo' => [
            'svg' => '<svg …>…</svg>',
            'url' => null,
            'text' => 'NS',
            'alt' => 'NotionScheduler',
            'preferred' => 'svg',
        ],
        'color' => null,
        'category' => 'social',
        'tags' => ['notion', 'planification', 'réseaux sociaux'],
        'meta' => [],
    ],
    // …
]
```

Même format en JSON (`Product` et `Logo` implémentent `JsonSerializable`), donc exposable tel quel dans une route API.

## Les logos

Chaque produit peut avoir **un ou plusieurs** formats. Le site choisit celui qu'il veut.

| Format | Propriété | Quand l'utiliser |
|---|---|---|
| SVG inline | `$logo->svg` | Recolorisable en CSS (`currentColor`), aucune requête. Idéal pour un footer. |
| Image distante | `$logo->url` | Logo en couleurs, PNG/WebP, ou hébergé par l'app elle-même. |
| Texte | `$logo->text` | Initiales ou emoji. Toujours présent (généré depuis le nom si absent). |
| Alt | `$logo->alt` | Texte alternatif (par défaut le nom). |

Méthodes utiles :

```php
$logo = Ecosystem::find('laredac')->logo;

$logo->hasSvg();                    // bool
$logo->hasUrl();                    // bool
$logo->preferred();                 // 'svg' | 'url' | 'text'
$logo->preferred(['url', 'svg']);   // ordre personnalisé

$logo->svg('size-6 text-white');    // HtmlString, classes fusionnées sur <svg>
$logo->img('size-6 rounded');       // HtmlString <img loading="lazy">
$logo->render('size-6');            // meilleur format dispo
$logo->render('size-6', ['url', 'text']); // ignore le SVG sur ce site
{{ $logo }}                         // équivalent à render() sans classe
```

**Astuce :** pour qu'un SVG prenne la couleur du texte du site, utilise `fill="currentColor"` ou `stroke="currentColor"` dans le fichier source.

## Référence de l'API

### `Ecosystem` (façade `Pr4w\Ecosystem\Facades\Ecosystem`)

| Méthode | Retour | Description |
|---|---|---|
| `others(?int $limit)` | `Collection<Product>` | **Ce qu'il faut afficher.** Actifs, hors exclusions, hors app courante. |
| `all()` | `Collection<Product>` | Actifs, hors exclusions, app courante incluse. |
| `catalog()` | `Collection<Product>` | Tout le catalogue brut, inactifs compris. |
| `current()` | `?Product` | L'app courante détectée. |
| `find(string $key)` | `?Product` | Un produit par clé. |
| `has(string $key)` | `bool` | |
| `isCurrent(Product $p)` | `bool` | |
| `toArray(?int $limit)` | `array` | `others()` sérialisé, prêt pour Inertia / JSON. |
| `flush()` | `void` | Vide le cache mémoire (tests). |

Les collections sont indexées par clé : `Ecosystem::others()->get('abrege')`, `->where('category', 'linkedin')`, `->shuffle()`, etc.

### `Product`

Propriétés en lecture seule : `key`, `name`, `url`, `tagline`, `description`, `logo` (`Logo`), `color`, `category`, `tags`, `meta`, `active`.

`url` est normalisée au chargement : une URL racine reçoit toujours un slash final (`https://abrege.app` → `https://abrege.app/`), pour que les UTM produisent `https://abrege.app/?utm_…`. Un chemin existant est conservé tel quel.

| Méthode | Description |
|---|---|
| `link(array $query = [])` | URL avec UTM. Les paramètres passés sont prioritaires : `link(['utm_content' => 'footer'])`. |
| `host()` | Domaine sans `www.` |
| `toArray()` / `jsonSerialize()` | Voir [Format du tableau](#format-du-tableau). |

## Générer la fiche d'une app avec Claude Code

Le package a besoin, pour chaque app, d'**une entrée dans `resources/products.php`** et d'**un SVG monochrome dans `resources/logos/`**. Plutôt que de les écrire à la main, ouvre Claude Code **dans le dépôt de l'app concernée** et colle le prompt ci-dessous : l'agent a accès au vrai logo, au vrai `APP_URL`, à la vraie meta description, et produit un bloc prêt à coller ici.

### Prompt 1 : produire la fiche catalogue (à lancer dans le dépôt de l'app)

````text
Je maintiens un package Composer privé `pr4w/laravel-ecosystem` qui centralise un catalogue
de mes apps pour afficher des liens croisés dans le footer de chacune. Je veux que tu produises
la fiche de CETTE app pour ce catalogue. Ne modifie aucun fichier de ce dépôt : tout ce que tu
produis va dans le package.

Fouille le projet (config/app.php, .env.example, resources/, public/, la landing page, les
balises <title> / meta description / og:image, tailwind.config.* ou app.css pour les couleurs
de marque, le favicon et les composants de logo) et rends-moi EXACTEMENT deux livrables.

LIVRABLE 1 — le fichier `<clé>.svg` (contenu complet dans un bloc de code) :
- Le logo de l'app en version MONOCHROME, destiné à être inliné dans un footer et à prendre
  la couleur du texte du site hôte.
- Obligatoire : attribut viewBox, PAS de width/height, PAS de prologue <?xml, PAS de
  commentaire, PAS de <style>, <script>, <defs> inutiles ni d'id.
- Toutes les couleurs remplacées par fill="currentColor" et/ou stroke="currentColor" ; les
  zones blanches ou transparentes du logo original restent fill="none".
- Si le logo est un symbole + un mot-marque, garde uniquement le symbole, cadré au carré.
- Léger : idéalement < 3 Ko. Simplifie les paths si besoin, sans dénaturer la marque.
- Si tu ne trouves aucune source vectorielle exploitable (PNG uniquement, logo trop
  complexe), dis-le clairement et fournis à la place l'URL publique absolue d'un logo en
  couleurs (PNG/SVG/WebP, ≥ 128 px, servi en https) à mettre dans 'logo.url'.

LIVRABLE 2 — l'entrée PHP à coller dans `resources/products.php` :

    '<clé>' => [
        'name' => '…',          // Nom exact de la marque, typographie incluse (apostrophes, accents, casse)
        'url' => 'https://…',   // URL de production canonique : https, sans www (le slash final est ajouté automatiquement)
        'tagline' => '…',       // 1 phrase, ≤ 80 caractères, se termine par un point, dit ce que l'app FAIT pour l'utilisateur
        'description' => '…',   // 2 phrases max, ≤ 200 caractères, complète la tagline sans la répéter
        'logo' => [
            'svg' => '<clé>.svg',       // le fichier du livrable 1 (omettre si pas de SVG)
            'url' => 'https://…',       // logo couleur public, si tu en as trouvé un (sinon omettre)
            'text' => '…',              // initiales (1 à 2 lettres majuscules) ou 1 emoji
        ],
        'color' => '#RRGGBB',   // couleur de marque principale (la couleur d'accent, pas le fond)
        'category' => '…',      // 1 mot minuscule : linkedin, social, productivité, freelance, …
        'tags' => ['…', '…', '…'],  // 3 à 5 mots-clés en minuscules, en français
    ],

Règles :
- <clé> = slug ASCII minuscule du nom, tirets autorisés, sans accent (ex. "laredac", "mission-monitor").
- Prends l'URL et les textes dans le code ou la landing de production, pas dans ta mémoire.
  Pour l'URL : APP_URL de .env.example, canonical de la landing, sitemap, config/app.php.
- Tout ce que tu n'as PAS pu vérifier dans le dépôt : mets la meilleure valeur possible suivie
  d'un commentaire `// TODO : à vérifier` sur la ligne. N'invente rien silencieusement.
- Textes en français, tutoiement interdit dans tagline/description (voix de marque neutre).
- Termine par une liste courte : ce que tu as trouvé où (fichier source du logo, source de la
  tagline, source de la couleur), et ce qui reste en TODO.
````

Ensuite, côté package :

1. Colle le SVG dans `resources/logos/<clé>.svg` et l'entrée dans `resources/products.php` (à la position d'affichage voulue).
2. `composer test` puis `php artisan ecosystem:list` dans une app pour contrôler le rendu.
3. Tag, push, `composer update pr4w/laravel-ecosystem` dans chaque app (voir la section suivante).

### Prompt 2 : brancher le package dans l'app (à lancer dans le dépôt de l'app)

Une fois l'app présente dans le catalogue et la version taguée :

````text
Installe et branche le package Composer privé `pr4w/laravel-ecosystem` dans ce projet Laravel.
Son README est ici : <colle l'URL du README ou son contenu>. En résumé : il expose
`Ecosystem::others()` (liste des autres apps : name, tagline, link(), logo->render()) et
`Ecosystem::toArray()` pour Inertia. La clé de cette app dans le catalogue est `<clé>`.

À faire :
1. Ajouter le dépôt VCS `git@github.com:pr4w/laravel-ecosystem.git` dans composer.json puis
   `composer require pr4w/laravel-ecosystem`.
2. Ajouter `ECOSYSTEM_CURRENT=<clé>` dans .env ET .env.example.
3. Afficher les autres apps dans le footer du site public (pas dans l'app connectée si le
   footer y est différent), en respectant le design system existant du projet : mêmes
   composants, mêmes tokens, même grille. Blade si le footer est en Blade, composant Vue via
   les props partagées Inertia si le footer est en Vue. Titre de la section : "Aussi par Mark"
   ou équivalent cohérent avec le ton du site.
4. Chaque lien : href = `$app->link()` (ou `app.href`), texte = nom + tagline, logo via
   `$app->logo->render('…')` (ou app.logo.preferred côté Vue). Liens normaux : pas de
   rel="nofollow", pas de target="_blank".
5. Lancer `php artisan ecosystem:list` et me montrer la sortie. Vérifier que cette app est
   bien marquée "courante (masquée)".
6. Ne rien changer d'autre. Me résumer les fichiers touchés.
````

## Ajouter ou modifier une app (mainteneur)

Tout se passe dans **`resources/products.php`** et **`resources/logos/`**.

1. Ajoute l'entrée dans `resources/products.php` :

   ```php
   'ma-nouvelle-app' => [
       'name' => 'Ma Nouvelle App',          // obligatoire
       'url' => 'https://manouvelleapp.fr',  // obligatoire
       'tagline' => 'Une phrase de pitch.',  // obligatoire
       'description' => 'Deux phrases de plus.',
       'logo' => [
           'svg' => 'ma-nouvelle-app.svg',   // fichier dans resources/logos
           'url' => 'https://manouvelleapp.fr/logo.png',
           'text' => 'MN',
       ],
       'color' => '#FF5A1F',
       'category' => 'productivité',
       'tags' => ['…'],
       'meta' => [],                         // libre (badge "nouveau", prix…)
       'active' => true,                     // false = pas encore affichée
   ],
   ```

   Raccourcis acceptés pour `logo` : `'fichier.svg'`, `'https://…/logo.png'` ou un SVG brut `'<svg …>…</svg>'`.

2. Dépose le SVG dans `resources/logos/` (le prologue XML et les commentaires sont retirés automatiquement).

3. Vérifie :

   ```bash
   composer test
   ```

   Les tests de `CatalogTest` valident le vrai catalogue : champs obligatoires, URLs, SVG présents, clés propres, pas de doublon de domaine, un visuel par app active.

4. Tag et pousse :

   ```bash
   git commit -am "Ajoute Ma Nouvelle App"
   git tag v1.1.0 && git push --tags
   ```

5. Dans **chaque app** :

   ```bash
   composer update pr4w/laravel-ecosystem
   ```

   puis déploie. Pour ne rien oublier, Dependabot ou Renovate peuvent ouvrir ces PR automatiquement.

**Règles :**
- Ne renomme jamais une clé déjà déployée : elle est utilisée par `ECOSYSTEM_CURRENT` et les UTM.
- L'ordre du tableau est l'ordre d'affichage.
- Pour retirer une app sans la supprimer : `'active' => false`.
- Changement de catalogue = version mineure (`v1.x`). Changement d'API = version majeure.

## Tests

```bash
composer install
composer test
```

Dans une app, pour tester avec un faux catalogue :

```php
config()->set('ecosystem.catalog', base_path('tests/Fixtures/ecosystem.php'));
\Pr4w\Ecosystem\Facades\Ecosystem::flush();
```

## Compatibilité

| Laravel | PHP | Testbench |
|---|---|---|
| 12.x | 8.2+ | 10.x |
| 13.x | 8.3+ | 11.x |
