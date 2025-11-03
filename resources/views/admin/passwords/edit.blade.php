<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
      {{ __('Editar contraseña') }}
    </h2>
  </x-slot>

  <div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm sm:rounded-xl p-6">
        <form method="POST" action="{{ route('admin.passwords.update', $entry) }}" class="space-y-4">
          @csrf @method('PUT')

          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Tipo</label>
              <input name="tipo" class="mt-1 w-full border rounded-xl px-3 py-2" required value="{{ $entry->tipo }}">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Aplicación</label>
              <input name="aplicacion" class="mt-1 w-full border rounded-xl px-3 py-2" required value="{{ $entry->aplicacion }}">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Estado</label>
              <input name="estado" class="mt-1 w-full border rounded-xl px-3 py-2" value="{{ $entry->estado }}">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Usuario</label>
              <input name="usuario" class="mt-1 w-full border rounded-xl px-3 py-2" value="{{ $entry->usuario }}">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Correo</label>
              <input name="correo" class="mt-1 w-full border rounded-xl px-3 py-2" value="{{ $entry->correo }}">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Contraseña (dejar en blanco para NO cambiar)</label>
              <input name="password" type="text" class="mt-1 w-full border rounded-xl px-3 py-2" placeholder="(sin cambios)">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha de creación</label>
              <input name="fecha_creacion" type="date" class="mt-1 w-full border rounded-xl px-3 py-2" value="{{ $entry->fecha_creacion }}">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha de eliminación</label>
              <input name="fecha_eliminacion" type="date" class="mt-1 w-full border rounded-xl px-3 py-2" value="{{ $entry->fecha_eliminacion }}">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Observaciones</label>
            <textarea name="observaciones" rows="3" class="mt-1 w-full border rounded-xl px-3 py-2">{{ $entry->observaciones }}</textarea>
          </div>

          <div class="flex justify-end gap-2">
            <a href="{{ route('admin.passwords.index') }}" class="px-4 py-2 rounded-xl border">Volver</a>
            <button class="px-4 py-2 rounded-xl bg-[#0D3B66] text-white hover:bg-[#0b3054]">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>
