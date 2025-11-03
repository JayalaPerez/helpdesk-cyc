<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
      Categorías de Ticket
    </h2>
  </x-slot>

  <div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

      @if(session('ok'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2">
          {{ session('ok') }}
        </div>
      @endif

      <div class="bg-white shadow-sm sm:rounded-xl p-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Listado de categorías</h3>
          <a href="{{ route('admin.dashboard') }}" class="text-sm text-zinc-500 hover:underline">
            ← Volver al panel
          </a>
        </div>

        {{-- Crear --}}
        <form method="POST" action="{{ route('admin.ticket-categories.store') }}" class="mb-4 flex gap-2 flex-wrap">
          @csrf
          <input name="name" class="border rounded-lg px-3 py-2" placeholder="Nombre de la categoría" required>
          <label class="inline-flex items-center gap-2 text-sm text-zinc-600">
            <input type="checkbox" name="is_active" value="1" checked>
            Activa
          </label>
          <button class="px-4 py-2 rounded-lg bg-[#0D3B66] text-white hover:bg-[#0b3054]">
            Crear
          </button>
        </form>

        <div class="overflow-x-auto">
          <table class="w-full text-sm border border-zinc-200 sm:rounded-xl overflow-hidden">
            <thead class="bg-[#0D3B66] text-white">
              <tr>
                <th class="px-3 py-2 text-left">Nombre</th>
                <th class="px-3 py-2 text-left">Activa</th>
                <th class="px-3 py-2 text-left">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white">
              @forelse($categories as $c)
                <tr>
                  <td class="px-3 py-2">{{ $c->name }}</td>
                  <td class="px-3 py-2">
                    @if($c->is_active)
                      <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs">Sí</span>
                    @else
                      <span class="inline-flex px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-700 text-xs">No</span>
                    @endif
                  </td>
                  <td class="px-3 py-2">
                    <div class="flex gap-2">
                      <form method="POST" action="{{ route('admin.ticket-categories.update', $c) }}" class="flex gap-2 items-center">
                        @csrf
                        @method('PUT')
                        <input name="name" value="{{ $c->name }}" class="border rounded px-2 py-1 text-sm">
                        <label class="inline-flex items-center gap-1 text-xs text-zinc-600">
                          <input type="checkbox" name="is_active" value="1" @checked($c->is_active)>
                          Activa
                        </label>
                        <button class="px-3 py-1 rounded bg-zinc-900 text-white text-xs">
                          Guardar
                        </button>
                      </form>

                      <form method="POST" action="{{ route('admin.ticket-categories.destroy', $c) }}"
                            onsubmit="return confirm('¿Eliminar esta categoría?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 rounded bg-rose-600 text-white text-xs">
                          Eliminar
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="px-3 py-4 text-center text-zinc-500">Sin categorías.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</x-app-layout>
