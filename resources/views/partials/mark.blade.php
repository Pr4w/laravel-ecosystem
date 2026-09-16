{{-- Le visuel d'un produit : SVG teinté par la couleur de marque, image, ou emoji. --}}
<span class="eco__mark" @if ($app->color) style="color: {{ $app->color }}" @endif>{{ $app->logo->render() }}</span>
