<!--
    Liens vers les autres apps de l'écosystème, pour un site Inertia.

    Les données viennent des props partagées, alimentées côté PHP par
    Ecosystem::toArray(). Voir le README, section « Inertia + Vue ».

        <EcosystemLinks variant="columns" />
        <EcosystemLinks variant="inline" heading="Nos autres outils" :limit="3" />

    Publié par :  php artisan vendor:publish --tag=ecosystem-vue
-->
<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  // 'inline' = ligne discrète, 'grid' = grille avec pitch, 'columns' = deux colonnes
  variant: { type: String, default: 'columns' },
  // Par défaut les props partagées ; passe une liste pour t'en affranchir.
  apps: { type: Array, default: null },
  heading: { type: String, default: null },
  limit: { type: Number, default: null },
  utmContent: { type: String, default: 'footer' },
})

const page = usePage()

const items = computed(() => {
  const source = props.apps ?? page.props.ecosystem ?? []

  return props.limit ? source.slice(0, props.limit) : source
})

const label = computed(() => props.heading ?? page.props.ecosystemHeading ?? null)

function href (app) {
  if (!props.utmContent) return app.href

  const url = new URL(app.href)
  url.searchParams.set('utm_content', props.utmContent)

  return url.toString()
}

const withPitch = computed(() => props.variant !== 'inline')
</script>

<template>
  <section v-if="items.length" class="eco" :class="`eco--${variant}`">
    <p v-if="label" class="eco__heading">{{ label }}</p>

    <component :is="variant === 'inline' ? 'nav' : 'div'" :class="`eco__${variant === 'inline' ? 'row' : variant}`">
      <template v-for="(app, index) in items" :key="app.key">
        <span v-if="variant === 'inline' && index > 0" class="eco__separator" aria-hidden="true">·</span>

        <a class="eco__item" :href="href(app)">
          <span class="eco__mark" :style="app.color ? { color: app.color } : undefined">
            <span
              v-if="app.logo.preferred === 'svg'"
              class="eco__svg"
              v-html="app.logo.svg"
            />
            <img
              v-else-if="app.logo.preferred === 'url'"
              :src="app.logo.url"
              :alt="app.logo.alt"
              loading="lazy"
              decoding="async"
            >
            <template v-else>{{ app.logo.text }}</template>
          </span>

          <span v-if="withPitch" class="eco__text">
            <strong class="eco__name">{{ app.name }}</strong>
            <small class="eco__pitch">{{ app.tagline }}</small>
          </span>
          <span v-else>{{ app.name }}</span>
        </a>
      </template>
    </component>
  </section>
</template>

<!--
    Mêmes styles que les vues Blade. « scoped » les isole du reste du site ;
    adapte-les librement, ce fichier t'appartient une fois publié.
-->
<style scoped>
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

.eco__mark > img,
.eco__svg {
  display: block;
  width: 100%;
  height: 100%;
}

.eco__svg :deep(svg) {
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

.eco__grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: var(--eco-gap);
}

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
