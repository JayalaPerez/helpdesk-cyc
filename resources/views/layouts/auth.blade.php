<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Auth' }} · Helpdesk C&C</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen">
  {{-- Fondo --}}
  <div class="fixed inset-0">
    <img src="{{ asset('images/login-bg.jpg') }}" class="w-full h-full object-cover" alt="">
    <div class="absolute inset-0 bg-zinc-900/65 backdrop-blur-[1px]"></div>
  </div>

  {{-- Contenido centrado --}}
  <div class="relative min-h-screen grid place-items-center p-4">
    <div class="w-full max-w-md bg-white/95 backdrop-blur rounded-2xl shadow-xl border border-zinc-100 p-6 md:p-8">
      <div class="flex flex-col items-center gap-2 mb-5">
        <a href="https://consultorescyc.cl/" target="_blank" rel="noreferrer"
           class="hover:opacity-90 transition">
          <img src="{{ asset('images/logo-cyc.png') }}" class="h-6 md:h-7" alt="C&C">
        </a>
        <h1 class="text-2xl font-bold">Helpdesk C&C</h1>
        @isset($subtitle)
          <p class="text-sm text-zinc-500">{{ $subtitle }}</p>
        @endisset
      </div>

      {{ $slot }}

      <p class="mt-6 text-center text-xs text-zinc-500">
        © {{ now()->year }} Consultores C&C — Todos los derechos reservados
      </p>
    </div>
  </div>
</body>
</html>
