{{-- Lista con tildes (en React era <Lista>) --}}
@props([
    'items',
    'chica' => false,
])

<ul @class(['landing-lista', 'landing-lista-chica' => $chica])>
    @foreach ($items as $item)
        <li><span class="landing-lista-check" aria-hidden="true">✓</span>{{ $item }}</li>
    @endforeach
</ul>
