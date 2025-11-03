<x-layouts.auth :title="'Confirmar contraseña'">
  <x-slot:subtitle>Por seguridad, confirma tu contraseña</x-slot:subtitle>

  <form method="POST" action="{{ route('password.confirm') }}" class="grid gap-4">
    @csrf
    <x-ui.input label="Contraseña" name="password" type="password" required autocomplete="current-password"/>
    <x-ui.button type="submit" class="w-full">Confirmar</x-ui.button>
  </form>
</x-layouts.auth>
