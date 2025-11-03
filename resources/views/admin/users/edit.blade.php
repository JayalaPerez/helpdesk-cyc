<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Editar usuario') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

        {{-- Errores de validación --}}
        @if ($errors->any())
          <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-2">
            <ul class="list-disc list-inside">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid gap-4">
          @csrf
          @method('PUT')

          <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input name="name" value="{{ old('name', $user->name) }}"
                   class="border rounded-xl p-2 w-full" required>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="border rounded-xl p-2 w-full" required>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Rol</label>
            @php $isMe = $user->id === auth()->id(); @endphp
            <select name="role" class="border rounded-xl p-2 w-full" {{ $isMe ? 'disabled' : '' }}>
              <option value="user"  @selected(old('role', $user->role) === 'user')>user</option>
              <option value="admin" @selected(old('role', $user->role) === 'admin')>admin</option>
            </select>
            {{-- Si está deshabilitado (cuando edito mi propio usuario), envío el valor igual --}}
            @if($isMe)
              <input type="hidden" name="role" value="{{ $user->role }}">
              <p class="text-xs text-zinc-500 mt-1">No puedes cambiar tu propio rol.</p>
            @endif
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Password (opcional)</label>
            <input type="password" name="password" class="border rounded-xl p-2 w-full"
                   placeholder="Dejar en blanco para no cambiar">
          </div>

          <div class="flex gap-2">
            <button class="px-4 py-2 rounded-xl bg-zinc-900 text-white">Guardar</button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-xl border">Cancelar</a>
          </div>
        </form>

      </div>
    </div>
  </div>
</x-app-layout>
