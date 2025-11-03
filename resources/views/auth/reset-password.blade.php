<x-layouts.auth :title="'Restablecer contraseña'">
  <x-slot:subtitle>Ingresa tu nueva contraseña</x-slot:subtitle>

  <form method="POST" action="{{ route('password.store') }}" class="grid gap-4">
    @csrf
    <input type="hidden" name="token" value="{{ request()->route('token') }}">

    <x-ui.input label="Correo electrónico" name="email" type="email" value="{{ old('email', request('email')) }}" required readonly class="bg-zinc-50"/>
    <x-ui.input label="Nueva contraseña" name="password" type="password" required autocomplete="new-password"/>
    <x-ui.input label="Confirmar contraseña" name="password_confirmation" type="password" required autocomplete="new-password"/>

    <x-ui.button type="submit" class="w-full">Actualizar contraseña</x-ui.button>
  </form>
</x-layouts.auth>
