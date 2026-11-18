<x-layouts::app title="Crear cuenta">
    <x-slot:head>
        @vite('resources/css/auth.css')
    </x-slot:head>

    <div class="auth">
        <h1>Crear cuenta</h1>

        <x-auth-session-status :status="session('status')" />

        {{-- Lo recibe Fortify en la ruta register.store (usa App\Actions\Fortify\CreateNewUser) --}}
        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <label for="name">Nombre</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Ana López">
            @error('name') <p class="error">{{ $message }}</p> @enderror

            <label for="email">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="ana@correo.com">
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="Contraseña">
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label for="password_confirmation">Repetir contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Repetir contraseña">

            <button type="submit" class="boton">Crear cuenta</button>
        </form>

        <p>¿Ya tenés cuenta? <a href="{{ route('login') }}">Iniciá sesión</a></p>
    </div>
</x-layouts::app>
