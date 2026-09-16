{{--
    E — Deux colonnes. Le pitch est là, la hauteur reste raisonnable sur mobile.

    <x-ecosystem::columns />
    <x-ecosystem::columns heading="Nos autres outils" utm-content="footer" />
--}}
@props([
    'apps' => null,
    'heading' => null,
    'limit' => null,
    'utmContent' => 'footer',
])

@php
    $apps ??= \Pr4w\Ecosystem\Facades\Ecosystem::others($limit);
    $heading ??= \Pr4w\Ecosystem\Facades\Ecosystem::heading();
@endphp

@if ($apps->isNotEmpty())
    @include('ecosystem::style')

    <section {{ $attributes->merge(['class' => 'eco eco--columns']) }}>
        @if ($heading)
            <p class="eco__heading">{{ $heading }}</p>
        @endif

        <div class="eco__columns">
            @foreach ($apps as $app)
                <a class="eco__item" href="{{ $app->link(array_filter(['utm_content' => $utmContent])) }}">
                    @include('ecosystem::partials.mark')
                    <span class="eco__text">
                        <strong class="eco__name">{{ $app->name }}</strong>
                        <small class="eco__pitch">{{ $app->tagline }}</small>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif
