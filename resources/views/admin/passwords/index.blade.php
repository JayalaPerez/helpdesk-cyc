<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
        {{ __('Gestor de contraseñas') }}
      </h2>
      <a href="{{ route('admin.dashboard') }}"
         class="text-sm text-indigo-700 hover:text-indigo-900 hover:underline">
        ← Volver al panel
      </a>
    </div>
  </x-slot>

  <div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- Mensaje de confirmación --}}
      @if(session('ok'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-2">
          {{ session('ok') }}
        </div>
      @endif

      {{-- Tarjeta principal --}}
      <div class="bg-white shadow-sm sm:rounded-xl p-4">
        <div class="flex items-center justify-between mb-3 gap-4">
          <h3 class="text-lg font-semibold">Contraseñas registradas</h3>
          <p class="text-sm text-gray-500">Total: {{ $entries->total() }}</p>
          <a href="{{ route('admin.passwords.create') }}"
             class="rounded-full bg-[#0D3B66] text-white px-4 py-2 hover:bg-[#0b3054] transition">
            + Nueva entrada
          </a>
        </div>

        {{-- 🔍 FILTROS --}}
        <form method="GET" class="mb-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

            {{-- Búsqueda general --}}
            <input name="q" value="{{ request('q') }}"
                   class="border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200"
                   placeholder="Buscar por aplicación, usuario o correo">

            {{-- Filtro por estado --}}
            <select name="estado"
                    class="border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
              <option value="">Estado (Todos)</option>
              @foreach(['Nuevo','Restringido','Eliminado','De baja'] as $estado)
                <option value="{{ $estado }}" @selected(request('estado')===$estado)>
                  {{ $estado }}
                </option>
              @endforeach
            </select>

            {{-- Filtro por tipo --}}
            @php
              $tipos = \App\Models\PasswordEntry::select('tipo')->distinct()->pluck('tipo');
            @endphp
            <select name="tipo"
                    class="border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
              <option value="">Tipo (Todos)</option>
              @foreach($tipos as $tipo)
                <option value="{{ $tipo }}" @selected(request('tipo')===$tipo)>
                  {{ $tipo }}
                </option>
              @endforeach
            </select>

            {{-- Botones --}}
            <div class="flex gap-2">
              <button class="rounded-xl bg-[#0D3B66] text-white px-4 py-2 hover:bg-[#0b3054] transition">
                Filtrar
              </button>
              @if(request()->hasAny(['q','estado','tipo']) && (request('q') || request('estado') || request('tipo')))
                <a href="{{ route('admin.passwords.index') }}"
                   class="rounded-xl px-4 py-2 border hover:bg-gray-100 transition">
                  Limpiar
                </a>
              @endif
            </div>
          </div>
        </form>

        {{-- TABLA --}}
        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-zinc-200 sm:rounded-xl overflow-hidden">
            <thead class="bg-[#0D3B66] text-white">
              <tr>
                <th class="px-3 py-2 text-left">Tipo</th>
                <th class="px-3 py-2 text-left">Aplicación</th>
                <th class="px-3 py-2 text-left">Estado</th>
                <th class="px-3 py-2 text-left">Usuario</th>
                <th class="px-3 py-2 text-left">Correo</th>
                <th class="px-3 py-2 text-left">Contraseña</th>
                <th class="px-3 py-2 text-left">Creación</th>
                <th class="px-3 py-2 text-left">Eliminación</th>
                <th class="px-3 py-2 text-left">Acciones</th>
                <th class="px-3 py-2 text-left">Observaciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white">
              @forelse($entries as $e)
                <tr>
                  <td class="px-3 py-2">{{ $e->tipo }}</td>
                  <td class="px-3 py-2">{{ $e->aplicacion }}</td>
                  <td class="px-3 py-2">{{ $e->estado }}</td>
                  <td class="px-3 py-2">{{ $e->usuario ?: '—' }}</td>
                  <td class="px-3 py-2">{{ $e->correo ?: '—' }}</td>

                  {{-- contraseña con ver/ocultar --}}
                  <td class="px-3 py-2">
                    <span class="font-mono tracking-wider">
                      <span class="pw-mask">••••••••</span>
                      <span class="pw-real hidden">{{ $e->password_decrypted ?? $e->password ?? '' }}</span>
                    </span>
                    <button type="button" class="ml-2 text-indigo-600 hover:underline pw-toggle">
                      ver
                    </button>
                  </td>

                  {{-- fechas --}}
                  <td class="px-3 py-2">
                    {{ $e->fecha_creacion ? \Carbon\Carbon::parse($e->fecha_creacion)->format('d-m-Y') : '—' }}
                  </td>
                  <td class="px-3 py-2">
                    {{ $e->fecha_eliminacion ? \Carbon\Carbon::parse($e->fecha_eliminacion)->format('d-m-Y') : '—' }}
                  </td>

                  {{-- acciones con íconos --}}
                  <td class="px-3 py-2">
                    <div class="flex items-center gap-3">

                      {{-- ✏️ Editar --}}
                      <a href="{{ route('admin.passwords.edit', $e) }}"
                         title="Editar"
                         class="text-indigo-600 hover:text-indigo-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                          <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.232 5.232a2.5 2.5 0 113.536 3.536L8.5 19.036H5v-3.572l10.232-10.232z" />
                        </svg>
                      </a>

                      {{-- ⚠️ Dar de baja --}}
                      @if($e->estado !== 'De baja')
                        <form method="POST" action="{{ route('admin.passwords.baja', $e) }}"
                              onsubmit="return confirm('¿Dar de baja esta cuenta?')">
                          @csrf
                          @method('PUT')
                          <button title="Dar de baja"
                                  class="text-amber-600 hover:text-amber-800 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                              <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h8" />
                            </svg>
                          </button>
                        </form>
                      @endif

                      {{-- 🗑️ Eliminar --}}
                      <form method="POST" action="{{ route('admin.passwords.destroy', $e) }}"
                            onsubmit="return confirm('¿Eliminar definitivamente esta entrada?')">
                        @csrf
                        @method('DELETE')
                        <button title="Eliminar definitivamente"
                                class="text-rose-600 hover:text-rose-800 transition">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                               viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </button>
                      </form>

                    </div>
                  </td>

                  {{-- observaciones --}}
                  <td class="px-3 py-2 max-w-xs break-words" title="{{ $e->observaciones }}">
                    {{ \Illuminate\Support\Str::limit($e->observaciones, 60, '…') ?: '—' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="px-3 py-6 text-center text-gray-500">
                    Sin contraseñas registradas.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- PAGINACIÓN --}}
        <div class="mt-4">
          {{ $entries->links() }}
        </div>
      </div>
    </div>
  </div>

  {{-- Mostrar/Ocultar contraseñas --}}
  <script>
    document.addEventListener('click', (e) => {
      if (!e.target.classList.contains('pw-toggle')) return;
      const td = e.target.closest('td');
      const mask = td.querySelector('.pw-mask');
      const real = td.querySelector('.pw-real');
      const showing = !real.classList.contains('hidden');

      if (showing) {
        real.classList.add('hidden');
        mask.classList.remove('hidden');
        e.target.textContent = 'ver';
      } else {
        real.classList.remove('hidden');
        mask.classList.add('hidden');
        e.target.textContent = 'ocultar';
      }
    });
  </script>
</x-app-layout>
