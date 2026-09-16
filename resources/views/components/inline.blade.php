{{--
    A — Ligne discrète. Une seule ligne, aucune emprise visuelle.

    <x-ecosystem::inline />
    <x-ecosystem::inline heading="Nos autres outils" :limit="3" utm-content="footer" />
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

    <section {{ $attributes->merge(['class' => 'eco eco--inline']) }}>
        @if ($heading)
            <p class="eco__heading">{{ $heading }}</p>
        @endif

        <nav class="eco__row">
            @foreach ($apps as $app)
                @if (! $loop->first)
                    <span class="eco__separator" aria-hidden="true">·</span>
                @endif

                <a class="eco__item" href="{{ $app->link(array_filter(['utm_content' => $utmContent])) }}">
                    @include('ecosystem::partials.mark')
                    <span>{{ $app->name }}</span>
                </a>
            @endforeach
        </nav>
    </section>
@endif
