<x-layouts.auth :title="'Recuperar contraseña'">
  <x-slot:subtitle>Te enviaremos un enlace a tu correo</x-slot:subtitle>

  @if (session('status'))
    <x-ui.alert type="success" class="mb-4">{{ session('status') }}</x-ui.alert>
  @endif

  <form method="POST" action="{{ route('password.email') }}" class="grid gap-4">
    @csrf
    <x-ui.input label="Correo electrónico" name="email" type="email" value="{{ old('email') }}" required autofocus/>
    <x-ui.button type="submit" class="w-full">Enviar enlace</x-ui.button>
    <x-ui.button as="a" href="{{ route('login') }}" variant="ghost" class="w-full">Volver a iniciar sesión</x-ui.button>
  </form>
</x-layouts.auth>
