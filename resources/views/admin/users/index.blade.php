<x-app-layout>
  <div class="max-w-7xl mx-auto p-6 space-y-6">
    @if(session('ok'))
      <div class="rounded-lg bg-green-100 text-green-800 px-4 py-2">{{ session('ok') }}</div>
    @endif
    @if($errors->any())
      <div class="rounded-lg bg-red-100 text-red-800 px-4 py-2">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold">Usuarios</h1>
        <p class="text-sm text-zinc-600">Gestiona usuarios y roles.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('tickets.index') }}" class="px-4 py-2 rounded-xl border">Gestionar Tickets</a>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 rounded-xl bg-zinc-900 text-white">Volver al Dashboard</a>
      </div>
    </div>

    {{-- Crear usuario --}}
    <div class="bg-white rounded-2xl border p-4">
      <h2 class="font-semibold mb-3">Crear usuario</h2>
      <form method="POST" action="{{ route('admin.users.store') }}" class="grid md:grid-cols-5 gap-3">
        @csrf
        <input name="name" class="border rounded-xl p-2" placeholder="Nombre" required>
        <input name="email" type="email" class="border rounded-xl p-2" placeholder="Email" required>
        <input name="password" type="password" class="border rounded-xl p-2" placeholder="Password (opcional)">
        <select name="role" class="border rounded-xl p-2">
          @foreach(['user','admin'] as $r)
            <option value="{{ $r }}">{{ $r }}</option>
          @endforeach
        </select>
        <button class="px-4 py-2 rounded-xl bg-zinc-900 text-white">Crear</button>
      </form>
      <p class="text-xs text-zinc-500 mt-2">Si no ingresas password, se usará <code>password123</code>.</p>
    </div>

    {{-- Tabla de usuarios --}}
    <div class="bg-white rounded-2xl border p-4">
      <table class="w-full text-sm">
        <thead>
          <tr class="text-left text-zinc-600">
            <th class="py-2">ID</th>
            <th class="py-2">Nombre</th>
            <th class="py-2">Email</th>
            <th class="py-2">Rol</th>
            <th class="py-2">Creado</th>
            <th class="py-2">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @foreach($users as $u)
            <tr>
              <td class="py-2">{{ $u->id }}</td>
              <td class="py-2">
                <form method="POST" action="{{ route('admin.users.update',$u) }}" class="flex gap-2 items-center">
                  @csrf @method('PUT')
                  <input name="name" class="border rounded-xl p-1" value="{{ old('name',$u->name) }}" required>
              </td>
              <td class="py-2">
                  <input name="email" class="border rounded-xl p-1" value="{{ old('email',$u->email) }}" required>
              </td>
              <td class="py-2">
                  <select name="role" class="border rounded-xl p-1">
                    @foreach(['user','admin'] as $r)
                      <option value="{{ $r }}" @selected(old('role',$u->role)===$r)>{{ $r }}</option>
                    @endforeach
                  </select>
              </td>
              <td class="py-2">{{ $u->created_at?->format('d-m-Y H:i') }}</td>
              <td class="py-2">
                  <div class="flex items-center gap-2">
                    <input name="password" type="password" class="border rounded-xl p-1" placeholder="Nuevo password (opcional)">
                    <button class="px-3 py-1 rounded-xl bg-zinc-900 text-white">Guardar</button>
                  </div>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy',$u) }}" class="inline"
                      onsubmit="return confirm('¿Eliminar usuario {{ $u->email }}?');">
                  @csrf @method('DELETE')
                  <button class="mt-2 px-3 py-1 rounded-xl bg-red-600 text-white"
                          @disabled(auth()->id()===$u->id)>Eliminar</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">{{ $users->links() }}</div>
    </div>
  </div>
</x-app-layout>
