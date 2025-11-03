<x-layouts.auth :title="'Crear cuenta'">
  <x-slot:subtitle>Regístrate para ingresar al helpdesk</x-slot:subtitle>

  @if ($errors->any())
    <x-ui.alert type="error" class="mb-4">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </x-ui.alert>
  @endif

  <form method="POST" action="{{ route('register') }}" class="grid gap-4">
    @csrf
    <x-ui.input label="Nombre" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"/>
    <x-ui.input label="Correo electrónico" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"/>
    <x-ui.input label="Contraseña" name="password" type="password" required autocomplete="new-password"/>
    <x-ui.input label="Confirmar contraseña" name="password_confirmation" type="password" required autocomplete="new-password"/>

    <x-ui.button type="submit" variant="primary" class="w-full">Crear cuenta</x-ui.button>
    <x-ui.button as="a" href="{{ route('login') }}" variant="ghost" class="w-full">¿Ya tienes cuenta? Inicia sesión</x-ui.button>
  </form>
</x-layouts.auth>
