<x-layouts::app title="Recuperar contraseña">
    <x-slot:head>
        @vite('resources/css/auth.css')
    </x-slot:head>

    <div class="auth">
        <h1>Recuperar contraseña</h1>
        <p>Poné tu correo y te mandamos un enlace para elegir una contraseña nueva.</p>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label for="email">Correo</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="ana@correo.com">
            @error('email') <p class="error">{{ $message }}</p> @enderror

            <button type="submit" class="boton">Mandar enlace</button>
        </form>

        <p><a href="{{ route('login') }}">Volver a iniciar sesión</a></p>
    </div>
</x-layouts::app>
