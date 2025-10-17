<x-app-layout>
  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Nuevo Ticket</h1>

    @if ($errors->any())
      <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-2">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('tickets.store') }}" class="grid gap-3">
      @csrf
      <input name="subject" class="border rounded-xl p-2" value="{{ old('subject') }}" placeholder="Asunto" required>
      <input name="department" class="border rounded-xl p-2" value="{{ old('department') }}" placeholder="Departamento (opcional)">
      <select name="priority" class="border rounded-xl p-2">
        @foreach(['Baja','Media','Alta','Crítica'] as $p)
          <option value="{{ $p }}" @selected(old('priority')===$p)>{{ $p }}</option>
        @endforeach
      </select>
      <select name="status" class="border rounded-xl p-2">
        @foreach(['Nuevo','En Progreso','Resuelto','Cerrado'] as $s)
          <option value="{{ $s }}" @selected(old('status')===$s)>{{ $s }}</option>
        @endforeach
      </select>
      <textarea name="description" class="border rounded-xl p-2 min-h-[120px]" placeholder="Describe el problema" required>{{ old('description') }}</textarea>
      <div class="flex gap-2">
        <button class="px-4 py-2 rounded-xl bg-zinc-900 text-white">Guardar</button>
        <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border">Cancelar</a>
      </div>
    </form>
  </div>
</x-app-layout>
