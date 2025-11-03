<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
      {{ __('Tickets') }}
    </h2>
  </x-slot>

  @php
    // Colores corporativos para badges
    $statusColors = [
      'Nuevo'       => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
      'En Progreso' => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
      'Resuelto'    => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
      'Cerrado'     => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-200',
    ];
    $priorityColors = [
      'Baja'    => 'bg-zinc-100 text-zinc-700 ring-1 ring-zinc-200',
      'Media'   => 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
      'Alta'    => 'bg-orange-50 text-orange-700 ring-1 ring-orange-200',
      'Crítica' => 'bg-rose-50 text-rose-700 ring-1 ring-rose-200',
    ];
  @endphp

  <div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      {{-- Flash --}}
      @if(session('ok'))
        <div class="mb-4 rounded-xl bg-emerald-50 text-emerald-800 px-4 py-2 border border-emerald-200">
          {{ session('ok') }}
        </div>
      @endif

      {{-- Toolbar --}}
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 mb-4">
        <h1 class="text-2xl font-bold text-[#0a2342]">Listado de Tickets</h1>

        <a href="{{ route('tickets.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#0a2342] text-white hover:bg-blue-800 transition shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5v14m-7-7h14"/></svg>
          Nuevo Ticket
        </a>
      </div>

      {{-- Filtros --}}
      <form method="GET" class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <input name="q" value="{{ request('q') }}"
                 class="border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200"
                 placeholder="Buscar por asunto, descripción, depto, categoría o usuario">

          <select name="status"
                  class="border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">Estado (Todos)</option>
            @foreach(['Nuevo','En Progreso','Resuelto','Cerrado'] as $s)
              <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
            @endforeach
          </select>

          <select name="priority"
                  class="border rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
            <option value="">Prioridad (Todas)</option>
            @foreach(['Baja','Media','Alta','Crítica'] as $p)
              <option value="{{ $p }}" @selected(request('priority')===$p)>{{ $p }}</option>
            @endforeach
          </select>

          <div class="flex gap-2">
            <button class="rounded-xl bg-[#0a2342] text-white px-4 py-2 hover:bg-blue-800 transition">Filtrar</button>
            @if(request()->hasAny(['q','status','priority']) && (request('q')||request('status')||request('priority')))
              <a href="{{ route('tickets.index') }}" class="rounded-xl px-4 py-2 border hover:bg-gray-50">Limpiar</a>
            @endif
          </div>
        </div>
      </form>

      {{-- Grid de tarjetas --}}
      @if($tickets->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach($tickets as $t)
            <a href="{{ route('tickets.show',$t) }}"
               class="group block rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-md transition">
              <div class="flex items-start justify-between gap-3">
                <h3 class="text-[17px] font-semibold text-[#0a2342] group-hover:text-blue-900">
                  {{ $t->subject }}
                </h3>
                {{-- Badges --}}
                <div class="shrink-0 flex flex-col items-end gap-1">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$t->status] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
                    {{ $t->status }}
                  </span>
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $priorityColors[$t->priority] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
                    {{ $t->priority }}
                  </span>
                </div>
              </div>

              <div class="mt-1 text-[13px] text-gray-600">
                <span class="font-semibold text-[#0a2342]">
                  {{ $t->requester?->display_name }}
                </span>
                · {{ $t->department ?? 'Sin depto.' }}
                @if($t->category)
                · {{ $t->category }}
                @endif
                @if($t->assignee)
                · Asignado: <span class="font-semibold text-[#0a2342]">{{ $t->assignee->display_name }}</span>
                @endif
                @if($t->closed_at)
                · Cerrado: {{ $t->closed_at->format('d-m-Y H:i') }}
                @endif
              </div>

              <p class="mt-3 text-sm text-gray-700 line-clamp-2">
                {{ $t->description }}
              </p>
            </a>
          @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
          {{ $tickets->onEachSide(1)->links() }}
        </div>
      @else
        {{-- Empty state --}}
        <div class="rounded-2xl bg-white border border-dashed border-gray-300 p-12 text-center text-gray-500 shadow-sm">
          <div class="mx-auto mb-3 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="currentColor">
              <path d="M7 3h10a2 2 0 0 1 2 2v11l-4-2-4 2-4-2-4 2V5a2 2 0 0 1 2-2h2z"/>
            </svg>
          </div>
          Aún no hay tickets que coincidan con el filtro.
          <div class="mt-4">
            <a href="{{ route('tickets.create') }}" class="inline-block px-4 py-2 rounded-xl bg-[#0a2342] text-white hover:bg-blue-800 transition">
              Crear mi primer ticket
            </a>
          </div>
        </div>
      @endif

    </div>
  </div>
</x-app-layout>
