<x-app-layout>

  @php
    // Obtener URL anterior
    $prev = url()->previous();

    // fallback seguro (por si viene desde otra ruta)
    $fallback = route('tickets.index');

    // determinar URL válida a la que volver
    $backUrl = \Illuminate\Support\Str::contains($prev, ['/dashboard', '/tickets'])
        ? $prev
        : $fallback;

    // colores para los estados y prioridades
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

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
        {{ __('Detalle del Ticket') }}
      </h2>

      <a href="{{ route('dashboard') }}"
        class="inline-flex items-center rounded-xl bg-[#0a2342] px-3 py-2 text-sm font-medium text-white hover:bg-blue-800 transition">
        ← Volver
      </a>
    </div>
  </x-slot>

  <div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 grid gap-6">

      {{-- Mensajes --}}
      @if(session('ok'))
        <div class="rounded-xl bg-emerald-50 text-emerald-800 px-4 py-2 border border-emerald-200">
          {{ session('ok') }}
        </div>
      @endif

      @if(session('error'))
        <div class="rounded-xl bg-rose-50 text-rose-800 px-4 py-2 border border-rose-200">
          {{ session('error') }}
        </div>
      @endif

      {{-- Tarjeta principal --}}
      <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
                {{ $ticket->status }}
              </span>
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-700 ring-1 ring-gray-200' }}">
                {{ $ticket->priority }}
              </span>
            </div>
            <h1 class="text-2xl font-bold text-[#0a2342]">{{ $ticket->subject }}</h1>
            <div class="text-sm text-gray-600 mt-1">
              <span class="font-semibold text-[#0a2342]">
                {{ $ticket->requester?->display_name }}
              </span>
              · {{ $ticket->department ?? 'Sin depto.' }}
              @if($ticket->category)
                · {{ $ticket->category }}
              @endif
              @if($ticket->assignee)
                · Asignado: <span class="font-semibold text-[#0a2342]">{{ $ticket->assignee->display_name }}</span>
              @endif
              @if($ticket->closed_at)
                · Cerrado: {{ $ticket->closed_at->format('d-m-Y H:i') }}
              @endif
            </div>
          </div>

          {{-- 🚫 Ocultar botón Editar si está cerrado y el usuario no es admin --}}
          @if($ticket->status !== 'Cerrado' || auth()->user()->isAdmin())
            <a href="{{ route('tickets.edit',$ticket) }}"
              class="shrink-0 px-3 py-2 rounded-xl border hover:bg-gray-50 transition">
              Editar
            </a>
          @endif
        </div>

        <p class="mt-4 whitespace-pre-wrap text-gray-800">{{ $ticket->description }}</p>
      </div>

      {{-- Acciones admin --}}
      @if(auth()->user()->isAdmin())
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-semibold text-[#0a2342] mb-4">Acciones de administración</h2>

          <form method="POST" action="{{ route('admin.tickets.adminUpdate', $ticket) }}"
                class="flex flex-col md:flex-row md:items-end gap-3">
            @csrf
            @method('PATCH')

            {{-- Estado --}}
            <div>
              <label class="block text-sm text-gray-700 mb-1">Estado</label>
              <select name="status"
                      class="border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200">
                @foreach(['Nuevo','En Progreso','Resuelto','Cerrado'] as $s)
                  <option value="{{ $s }}" @selected($ticket->status===$s)>{{ $s }}</option>
                @endforeach
              </select>
            </div>

            {{-- Asignar a --}}
            <div>
              <label class="block text-sm text-gray-700 mb-1">Asignar a</label>
              <select name="assigned_user_id"
                      class="border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200 min-w-[180px]">
                <option value="">— Sin asignar —</option>
                @foreach($admins as $a)
                  <option value="{{ $a->id }}" @selected($ticket->assigned_user_id===$a->id)>
                    {{ $a->name ?: $a->email }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Botón --}}
            <div>
              <button class="px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                Actualizar
              </button>
            </div>
          </form>
        </div>
      @endif

      {{-- Comentarios --}}
      <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-[#0a2342] mb-3">Comentarios</h2>

        <div class="grid gap-3">
          @forelse($ticket->comments as $c)
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-3">
              <div class="flex items-start justify-between gap-3 mb-1">
                <div>
                  <span class="font-semibold text-[#0a2342]">
                    {{ $c->author?->display_name }}
                  </span>
                  <span class="text-xs text-gray-500 ml-2">
                    {{ $c->created_at->format('d-m-Y H:i') }}
                  </span>
                </div>

                @if($c->attachment_path)
                  <a href="{{ route('comments.download', $c) }}"
                     class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 underline">
                    📎 {{ $c->attachment_name ?? 'Archivo adjunto' }}
                  </a>
                @endif
              </div>

              <div class="pl-1 text-sm text-gray-800 whitespace-pre-line">
                {{ $c->body }}
              </div>
            </div>
          @empty
            <div class="text-sm text-gray-500">Sin comentarios.</div>
          @endforelse
        </div>

        {{-- Formulario de comentario con adjunto --}}
        <form method="POST"
              action="{{ route('tickets.comments.store',$ticket) }}"
              enctype="multipart/form-data"
              class="mt-4 flex flex-col gap-4 md:flex-row md:flex-wrap md:items-end">
          @csrf

          {{-- Comentario --}}
          <div class="flex-1 min-w-[250px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Comentario</label>
            <input name="body"
                   class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200"
                   placeholder="Escribe un comentario..."
                   required>
          </div>

          {{-- Archivo --}}
          <div class="min-w-[250px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Adjuntar archivo (opcional)
            </label>
            <input type="file" name="attachment" class="block w-full text-sm text-gray-700">
            <p class="text-[11px] text-gray-400 mt-1">
              PDF / Imágenes / DOCX / XLSX — máx 2MB
            </p>
          </div>

          {{-- Botón --}}
          <div>
            <button class="px-4 py-2 rounded-xl bg-[#0a2342] text-white hover:bg-blue-800 transition">
              Enviar
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</x-app-layout>

