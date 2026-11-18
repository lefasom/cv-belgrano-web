{{-- Muestra un aviso de Fortify (por ejemplo "te mandamos el enlace") si hay uno en la sesión --}}
@props([
    'status',
])

@if ($status)
    <p class="aviso">{{ $status }}</p>
@endif
