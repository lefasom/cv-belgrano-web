{{-- Pasos numerados (en React era <Pasos>) --}}
@props([
    'items',
    'clase' => '',
])

<ol class="landing-instalar {{ $clase }}">
    @foreach ($items as $paso)
        <li>
            {{-- $loop->iteration es el número de vuelta del @foreach (1, 2, 3…) --}}
            <span class="landing-galeria-numero">{{ $loop->iteration }}</span>
            <div>
                <h3>{{ $paso['t'] }}</h3>
                <p>{{ $paso['d'] }}</p>
            </div>
        </li>
    @endforeach
</ol>
