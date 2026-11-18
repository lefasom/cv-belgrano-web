{{-- Cuatro columnas de título + texto (en React era <Columnas>) --}}
@props(['items'])

<div class="landing-siempre-columnas landing-columnas-cuatro">
    @foreach ($items as $columna)
        <div>
            <h3>{{ $columna['t'] }}</h3>
            <p>{{ $columna['d'] }}</p>
        </div>
    @endforeach
</div>
