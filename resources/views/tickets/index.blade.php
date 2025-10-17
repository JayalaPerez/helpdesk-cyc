<x-app-layout>
  <div class="max-w-7xl mx-auto p-6">
    @if(session('ok'))
      <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-2">
        {{ session('ok') }}
      </div>
    @endif

    <div class="flex items-end justify-between gap-4">
      <h1 class="text-2xl font-bold">Tickets</h1>
      <a href="{{ route('tickets.create') }}" class="px-4 py-2 rounded-xl bg-zinc-900 text-white">Nuevo Ticket</a>
    </div>

    <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
      <input name="q" value="{{ request('q') }}" class="border rounded-xl p-2" placeholder="Buscar...">
      <select name="status" class="border rounded-xl p-2">
        <option value="">Estado (Todos)</option>
        @foreach(['Nuevo','En Progreso','Resuelto','Cerrado'] as $s)
          <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
        @endforeach
      </select>
      <select name="priority" class="border rounded-xl p-2">
        <option value="">Prioridad (Todas)</option>
        @foreach(['Baja','Media','Alta','Crítica'] as $p)
          <option value="{{ $p }}" @selected(request('priority')===$p)>{{ $p }}</option>
        @endforeach
      </select>
      <button class="rounded-xl bg-zinc-900 text-white px-4 py-2">Filtrar</button>
    </form>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      @forelse($tickets as $t)
        <a href="{{ route('tickets.show',$t) }}" class="block rounded-2xl border p-4 bg-white shadow-sm hover:shadow">
          <div class="font-semibold">{{ $t->subject }}</div>
          <div class="text-sm text-zinc-600 mt-1">
            {{ $t->requester->email }} · {{ $t->department ?? 'Sin depto.' }} · {{ $t->status }}
          </div>
          <div class="mt-2 text-sm text-zinc-700 line-clamp-2">{{ $t->description }}</div>
        </a>
      @empty
        <div class="col-span-full text-zinc-500">Sin tickets.</div>
      @endforelse
    </div>

    <div class="mt-6">{{ $tickets->links() }}</div>
  </div>
</x-app-layout>
