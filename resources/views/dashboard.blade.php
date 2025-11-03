<x-app-layout>
  {{-- Encabezado --}}
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      Dashboard
    </h2>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

      {{-- Bienvenida + acciones --}}
      <div class="bg-white shadow-sm sm:rounded-xl p-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h3 class="text-lg font-semibold">¡Bienvenido/a {{ auth()->user()->name }}!</h3>
            <p class="text-sm text-zinc-600">
              Desde aquí puedes gestionar tus tickets o acceder al panel administrativo.
            </p>
          </div>

          <div class="flex flex-col sm:flex-row gap-2">
            @if(auth()->user()->isAdmin())
              <a href="{{ route('admin.dashboard') }}"
                 class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-500">
                <x-heroicon-s-cog-6-tooth class="w-4 h-4"/>
                Panel Admin
              </a>
            @endif
            <a href="{{ route('tickets.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-[#0D3B66] px-4 py-2 text-white hover:bg-[#0b3054]">
              <x-heroicon-s-ticket class="w-4 h-4"/>
              Ir a Tickets
            </a>
          </div>
        </div>
      </div>

      {{-- Mis tickets por estado --}}
      <div class="bg-white shadow-sm sm:rounded-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Mis Tickets por Estado</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          @foreach($statuses as $s)
            <a href="{{ route('tickets.index', ['status'=>$s]) }}"
               class="block rounded-2xl border p-4 hover:shadow transition">
              <div class="text-sm text-zinc-600">{{ $s }}</div>
              <div class="text-2xl font-bold">{{ $mine[$s] ?? 0 }}</div>
            </a>
          @endforeach
        </div>
      </div>

      {{-- Resumen global (solo admin) --}}
      @if(auth()->user()->isAdmin())
        <div class="bg-white shadow-sm sm:rounded-xl p-6">
          <h3 class="text-lg font-semibold mb-4">Resumen Global (Admin)</h3>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            @foreach($statuses as $s)
              <a href="{{ route('tickets.index', ['status'=>$s]) }}"
                 class="block rounded-2xl border p-4 hover:shadow transition">
                <div class="text-sm text-zinc-600">{{ $s }}</div>
                <div class="text-2xl font-bold">{{ $all[$s] ?? 0 }}</div>
              </a>
            @endforeach
          </div>

          {{-- Flash de eliminación / acciones --}}
          @if(session('ok'))
            <div class="mb-4 rounded-xl bg-emerald-50 text-emerald-800 px-4 py-2 border border-emerald-200">
              {{ session('ok') }}
            </div>
          @endif

          {{-- Últimos tickets --}}
          <h4 class="text-base font-semibold mb-2">Últimos tickets</h4>

          <div class="overflow-x-auto">
            <table class="w-full text-sm border border-zinc-200 sm:rounded-xl overflow-hidden">
              <thead class="bg-[#0D3B66] text-white">
                <tr>
                  <th class="px-3 py-2 text-left">ID</th>
                  <th class="px-3 py-2 text-left">Asunto</th>
                  <th class="px-3 py-2 text-left">Solicitante</th>
                  <th class="px-3 py-2 text-left">Depto</th>
                  <th class="px-3 py-2 text-left">Categoría</th>
                  <th class="px-3 py-2 text-left">Prioridad</th>
                  <th class="px-3 py-2 text-left">Estado</th>
                  <th class="px-3 py-2 text-left">Asignado</th>
                  <th class="px-3 py-2 text-left">Creado</th>   {{-- NUEVO --}}
                  <th class="px-3 py-2 text-left">Cerrado</th>
                  <th class="px-3 py-2 text-left">Acciones</th> {{-- NUEVO --}}
                </tr>
              </thead>

              <tbody class="divide-y divide-zinc-200 bg-white">
                @forelse($lastTickets as $t)
                  @php
                    $statusBadge = [
                      'Nuevo'       => 'bg-blue-100 text-blue-700',
                      'En Progreso' => 'bg-amber-100 text-amber-700',
                      'Resuelto'    => 'bg-emerald-100 text-emerald-700',
                      'Cerrado'     => 'bg-zinc-200 text-zinc-700',
                    ][$t->status] ?? 'bg-zinc-100 text-zinc-700';

                    $prioBadge = [
                      'Baja'    => 'bg-green-100 text-green-700',
                      'Media'   => 'bg-yellow-100 text-yellow-700',
                      'Alta'    => 'bg-orange-100 text-orange-700',
                      'Crítica' => 'bg-red-100 text-red-700',
                    ][$t->priority] ?? 'bg-zinc-100 text-zinc-700';
                  @endphp

                  <tr class="hover:bg-zinc-50">
                    {{-- ID --}}
                    <td class="px-3 py-2">
                      <a href="{{ route('tickets.show',$t) }}" class="text-indigo-600 hover:underline">
                        #{{ $t->id }}
                      </a>
                    </td>

                    {{-- Asunto --}}
                    <td class="px-3 py-2 font-semibold text-[#0a2342]">
                      <a href="{{ route('tickets.show',$t) }}" class="hover:underline">
                        {{ $t->subject }}
                      </a>
                    </td>

                    {{-- Solicitante --}}
                    <td class="px-3 py-2">
                      <span class="font-semibold text-[#0a2342]">
                        {{ $t->requester?->display_name }}
                      </span>
                    </td>

                    {{-- Depto --}}
                    <td class="px-3 py-2">{{ $t->department ?? '—' }}</td>

                    {{-- Categoría --}}
                    <td class="px-3 py-2">{{ $t->category ?? '—' }}</td>

                    {{-- Prioridad (pill) --}}
                    <td class="px-3 py-2">
                      <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $prioBadge }}">
                        {{ $t->priority }}
                      </span>
                    </td>

                    {{-- Estado (pill) --}}
                    <td class="px-3 py-2">
                      <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusBadge }}">
                        {{ $t->status }}
                      </span>
                    </td>

                    {{-- Asignado --}}
                    <td class="px-3 py-2">
                      @if($t->assignee)
                        <span class="font-semibold text-[#0a2342]">
                          {{ $t->assignee->display_name }}
                        </span>
                      @else
                        —
                      @endif
                    </td>

                    {{-- Creado (NUEVO) --}}
                    <td class="px-3 py-2 text-gray-700">
                      {{ $t->created_at?->format('d-m-Y H:i') ?? '—' }}
                    </td>

                    {{-- Cerrado --}}
                    <td class="px-3 py-2 text-gray-700">
                      {{ $t->closed_at ? $t->closed_at->format('d-m-Y H:i') : '—' }}
                    </td>

                    {{-- Acciones (solo admin) --}}
                    <td class="px-3 py-2 text-gray-700">
                      <form method="POST"
                            action="{{ route('admin.tickets.destroy', $t) }}"
                            onsubmit="return confirm('¿Seguro que quieres eliminar este ticket? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button
                          class="text-red-600 hover:text-red-800 text-xs font-semibold">
                          Eliminar
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="11" class="px-3 py-6 text-center text-gray-500">
                      No hay tickets todavía.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endif

    </div>
  </div>
</x-app-layout>
