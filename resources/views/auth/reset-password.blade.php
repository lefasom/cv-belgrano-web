<x-layouts::app title="Nueva contraseña">
    <x-slot:head>
        @vite('resources/css/auth.css')
    </x-slot:head>

    <div class="auth">
        <h1>Nueva contraseña</h1>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            {{-- El token viene en el enlace del correo --}}
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <label for="email">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email', request('email')) }}" required autocomplete="email">
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Contraseña nueva</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label for="password_confirmation">Repetir contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

            <button type="submit" class="boton">Guardar contraseña</button>
        </form>
    </div>
</x-layouts::app>
