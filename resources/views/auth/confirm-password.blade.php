<x-layouts::app title="Confirmar contraseña">
    <x-slot:head>
        @vite('resources/css/auth.css')
    </x-slot:head>

    <div class="auth">
        <h1>Confirmar contraseña</h1>
        <p>Es una parte protegida. Poné tu contraseña para seguir.</p>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}">
            @csrf

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <button type="submit" class="boton">Confirmar</button>
        </form>
    </div>
</x-layouts::app>
