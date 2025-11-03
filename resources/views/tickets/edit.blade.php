<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
      {{ __('Editar Ticket') }}
    </h2>
  </x-slot>

  <div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

      @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-2 border border-red-200">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('tickets.update',$ticket) }}"
            class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 grid gap-4">
        @csrf @method('PUT')

        <input name="subject" value="{{ old('subject',$ticket->subject) }}"
               class="border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200" required>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <select name="department"
                  class="border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200">
            <option value="">Seleccione departamento</option>
            @foreach($departments as $d)
              <option value="{{ $d }}" @selected(old('department',$ticket->department)===$d)>{{ $d }}</option>
            @endforeach
          </select>

          <select name="category"
                  class="border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200">
            <option value="">Seleccione categoría</option>
            @foreach($categories as $c)
              <option value="{{ $c }}" @selected(old('category',$ticket->category)===$c)>{{ $c }}</option>
            @endforeach
          </select>
        </div>

        <select name="priority"
                class="border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200" required>
          @foreach(['Baja','Media','Alta','Crítica'] as $p)
            <option value="{{ $p }}" @selected(old('priority',$ticket->priority)===$p)>{{ $p }}</option>
          @endforeach
        </select>

        {{-- Estado solo visible a admin --}}
        @if(auth()->user()->isAdmin())
          <select name="status"
                  class="border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200">
            @foreach(['Nuevo','En Progreso','Resuelto','Cerrado'] as $s)
              <option value="{{ $s }}" @selected(old('status',$ticket->status)===$s)>{{ $s }}</option>
            @endforeach
          </select>
        @endif

        <textarea name="description" rows="6"
                  class="border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200" required>{{ old('description',$ticket->description) }}</textarea>

        <div class="flex gap-2">
          <button class="px-4 py-2 rounded-xl bg-[#0a2342] text-white hover:bg-blue-800 transition">
            Actualizar
          </button>
          <a href="{{ route('tickets.show',$ticket) }}"
             class="px-4 py-2 rounded-xl border hover:bg-gray-50">Volver</a>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
