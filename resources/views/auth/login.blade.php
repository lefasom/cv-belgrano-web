<x-layouts::app title="Iniciar sesión">
    <x-slot:head>
        @vite('resources/css/auth.css')
    </x-slot:head>

    <div class="auth">
        <h1>Iniciar sesión</h1>

        <x-auth-session-status :status="session('status')" />

        {{-- Formulario HTML común: lo recibe Fortify en la ruta login.store --}}
        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="ana@correo.com">
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Contraseña</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="Tu contraseña">
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label class="auth-recordar">
                <input type="checkbox" name="remember" @checked(old('remember'))>
                Recordarme
            </label>

            <button type="submit" class="boton">Entrar</button>
        </form>

        @if (Route::has('password.request'))
            <p><a href="{{ route('password.request') }}">¿Te olvidaste la contraseña?</a></p>
        @endif

        <p>¿No tenés cuenta? <a href="{{ route('register') }}">Creá una</a></p>
    </div>
</x-layouts::app>
