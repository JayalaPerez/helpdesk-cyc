<x-app-layout>
  <div class="max-w-4xl mx-auto p-6 grid gap-4">
    @if(session('ok'))
      <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-2">
        {{ session('ok') }}
      </div>
    @endif

    <div class="rounded-2xl border p-4 bg-white">
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-2xl font-bold">{{ $ticket->subject }}</h1>
          <div class="text-sm text-zinc-600 mt-1">
            {{ $ticket->requester->email }} · {{ $ticket->department ?? 'Sin depto.' }} · {{ $ticket->priority }} · {{ $ticket->status }}
          </div>
        </div>
        @if(auth()->id()===$ticket->user_id || auth()->user()->isAdmin())
          <a href="{{ route('tickets.edit',$ticket) }}" class="px-3 py-2 rounded-xl border">Editar</a>
        @endif
      </div>
      <p class="mt-4 whitespace-pre-wrap">{{ $ticket->description }}</p>
    </div>

    <div class="rounded-2xl border p-4 bg-white">
      <h2 class="font-semibold mb-2">Comentarios</h2>
      <div class="grid gap-3">
        @forelse($ticket->comments as $c)
          <div class="rounded-xl border p-3">
            <div class="text-xs text-zinc-500">{{ $c->author->email }} · {{ $c->created_at->format('d-m-Y H:i') }}</div>
            <div class="mt-1 text-sm">{{ $c->body }}</div>
          </div>
        @empty
          <div class="text-sm text-zinc-500">Sin comentarios.</div>
        @endforelse
      </div>
      <form method="POST" action="{{ route('tickets.comments.store',$ticket) }}" class="mt-3 flex gap-2">
        @csrf
        <input name="body" class="flex-1 border rounded-xl p-2" placeholder="Escribe un comentario..." required>
        <button class="px-3 py-2 rounded-xl bg-zinc-900 text-white">Enviar</button>
      </form>
    </div>
  </div>
</x-app-layout>
