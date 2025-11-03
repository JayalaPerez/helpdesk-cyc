<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Helpdesk C&C</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: url('{{ asset('images/FondoLogin.png') }}') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Inter', sans-serif;
        }
        .overlay {
            background: rgba(0, 33, 71, 0.85); /* Azul corporativo con transparencia */
            position: fixed;
            inset: 0;
            z-index: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(6px);
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        .brand-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #002147; /* Azul C&C */
        }
        .brand-subtitle {
            font-size: 0.9rem;
            color: #555;
        }
        .btn-primary {
            background-color: #002147;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #004080;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative">

    <div class="overlay"></div>

    <div class="login-card w-full max-w-md mx-auto p-8 relative z-10">
        <div class="text-center mb-8">
            <a href="https://consultorescyc.cl/" target="_blank" title="Ir al sitio Consultores C&C">
                <img src="{{ asset('images/logo-cyc.png') }}" 
                    alt="C&C Consultores" 
                    class="w-50 mx-auto mb-3 hover:scale-105 transition-transform duration-300">
            </a>
            <h1 class="brand-title">Helpdesk C&C</h1>
            <p class="brand-subtitle">Sistema de soporte y gestión interna</p>
        </div>

        {{-- Mensajes flash --}}
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        {{-- Formulario de login --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">
                    Correo electrónico
                </label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                    Contraseña
                </label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-4">
                <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="mr-2">
                    Recuérdame
                </label>
            </div>

            <button type="submit" class="w-full py-2 rounded-lg text-white font-semibold btn-primary">
                Iniciar sesión
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            © {{ date('Y') }} Consultores C&C - Todos los derechos reservados
        </p>
    </div>
</body>
</html>
