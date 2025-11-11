<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
      {{ __('Editar contraseña') }}
    </h2>
  </x-slot>

  @php
    $tipos = ['Cuenta de correo', 'Cuenta de aplicación', 'Cuenta Bitácora', 'Cuenta Helpdesk', 'Otro'];
    $aplicaciones = ['Outlook', 'Google Drive', 'Brevo', 'V2NETWORKS', 'Google Correo', 'Aula Virtual', 'Bitácora C&C', 'Otro'];
    $estados = ['Nuevo', 'Eliminado', 'Restringido'];
  @endphp

  <div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow-sm sm:rounded-xl p-6">
        <form method="POST" action="{{ route('admin.passwords.update', $entry) }}" class="space-y-4">
          @csrf @method('PUT')

          <div class="grid md:grid-cols-2 gap-4">
            {{-- TIPO --}}
            <div>
              <label class="block text-sm font-medium text-gray-700">Tipo</label>
              <select name="tipo" class="mt-1 w-full border rounded-xl px-3 py-2" required>
                @foreach($tipos as $t)
                  <option value="{{ $t }}" @selected($entry->tipo === $t)>{{ $t }}</option>
                @endforeach
              </select>
            </div>

            {{-- APLICACIÓN --}}
            <div>
              <label class="block text-sm font-medium text-gray-700">Aplicación</label>
              <select name="aplicacion" class="mt-1 w-full border rounded-xl px-3 py-2" required>
                @foreach($aplicaciones as $a)
                  <option value="{{ $a }}" @selected($entry->aplicacion === $a)>{{ $a }}</option>
                @endforeach
              </select>
            </div>

            {{-- ESTADO --}}
            <div>
              <label class="block text-sm font-medium text-gray-700">Estado</label>
              <select name="estado" class="mt-1 w-full border rounded-xl px-3 py-2">
                @foreach($estados as $e)
                  <option value="{{ $e }}" @selected($entry->estado === $e)>{{ $e }}</option>
                @endforeach
              </select>
            </div>

            {{-- USUARIO --}}
            <div>
              <label class="block text-sm font-medium text-gray-700">Usuario</label>
              <input name="usuario" class="mt-1 w-full border rounded-xl px-3 py-2"
                     value="{{ $entry->usuario }}">
            </div>

            {{-- CORREO --}}
            <div>
              <label class="block text-sm font-medium text-gray-700">Correo</label>
              <input name="correo" class="mt-1 w-full border rounded-xl px-3 py-2"
                     value="{{ $entry->correo }}">
            </div>

            {{-- CONTRASEÑA --}}
            <div>
              <label class="block text-sm font-medium text-gray-700">
                Contraseña (dejar en blanco para NO cambiar)
              </label>
              <input name="password" type="text"
                     class="mt-1 w-full border rounded-xl px-3 py-2"
                     placeholder="(sin cambios)">
            </div>

            {{-- FECHA CREACIÓN --}}
            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha de creación</label>
              <input name="fecha_creacion" type="date"
                     class="mt-1 w-full border rounded-xl px-3 py-2"
                     value="{{ $entry->fecha_creacion }}">
            </div>

            {{-- FECHA ELIMINACIÓN --}}
            <div>
              <label class="block text-sm font-medium text-gray-700">Fecha de eliminación</label>
              <input name="fecha_eliminacion" type="date"
                     class="mt-1 w-full border rounded-xl px-3 py-2"
                     value="{{ $entry->fecha_eliminacion }}">
            </div>
          </div>

          {{-- OBSERVACIONES --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Observaciones</label>
            <textarea name="observaciones" rows="3"
                      class="mt-1 w-full border rounded-xl px-3 py-2">{{ $entry->observaciones }}</textarea>
          </div>

          {{-- BOTONES --}}
          <div class="flex justify-end gap-2">
            <a href="{{ route('admin.passwords.index') }}" class="px-4 py-2 rounded-xl border">Volver</a>
            <button class="px-4 py-2 rounded-xl bg-[#0D3B66] text-white hover:bg-[#0b3054]">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-app-layout>

