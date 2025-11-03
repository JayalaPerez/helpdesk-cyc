<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
      {{ __('Panel de Administración') }}
    </h2>
  </x-slot>

  <div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6">

        <h1 class="text-2xl font-bold text-[#0a2342] mb-2">Bienvenido, Administrador</h1>
        <p class="mb-6 text-gray-600">Administra usuarios y tickets del sistema Helpdesk.</p>

        @if(session('ok'))
          <div class="mb-4 rounded-xl bg-emerald-50 text-emerald-800 px-4 py-2 border border-emerald-200">
            {{ session('ok') }}
          </div>
        @endif
        @if(session('error'))
          <div class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-2 border border-red-200">
            {{ session('error') }}
          </div>
        @endif

        {{-- Botones principales --}}
        <div class="mb-6 flex gap-3">
          <a href="{{ route('tickets.index') }}"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#0a2342] text-white hover:bg-blue-800 transition shadow-sm">
            Gestionar Tickets
          </a>
          <a href="{{ route('dashboard') }}"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
            Volver al Dashboard
          </a>
        </div>

        {{-- Accesos rápidos de configuración --}}
        @if(auth()->user()->isAdmin())
          <div class="mb-8 bg-white border border-gray-100 rounded-2xl p-5">
            <h3 class="text-lg font-semibold mb-4 text-[#0a2342]">Configuración de helpdesk</h3>
            <div class="flex flex-wrap gap-3">
              <a href="{{ route('admin.departments.index') }}"
                 class="px-4 py-2 rounded-lg bg-[#0D3B66] text-white hover:bg-[#0b3054]">
                Departamentos
              </a>
              <a href="{{ route('admin.ticket-categories.index') }}"
                 class="px-4 py-2 rounded-lg bg-[#0D3B66] text-white hover:bg-[#0b3054]">
                Categorías de ticket
              </a>
              <a href="{{ route('admin.passwords.index') }}"
                 class="px-4 py-2 rounded-lg bg-[#0D3B66] text-white hover:bg-[#0b3054]">
                Gestor de contraseñas
              </a>
            </div>
          </div>
        @endif

        {{-- Crear usuario --}}
        <h2 class="text-lg font-semibold text-[#0a2342] mb-3">Crear usuario</h2>
        @if ($errors->any())
          <div class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-2 border border-red-200">
            <ul class="list-disc list-inside">
              @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
          </div>
        @endif
        <form method="POST" action="{{ route('admin.users.store') }}"
              class="mb-8 grid grid-cols-1 md:grid-cols-5 gap-3">
          @csrf
          <input name="name" value="{{ old('name') }}" class="border rounded-xl px-3 py-2" placeholder="Nombre" required>
          <input name="email" value="{{ old('email') }}" type="email" class="border rounded-xl px-3 py-2" placeholder="Email" required>
          <input name="password" type="password" class="border rounded-xl px-3 py-2" placeholder="Password" required>
          <select name="role" class="border rounded-xl px-3 py-2" required>
            <option value="user" @selected(old('role')==='user')>user</option>
            <option value="admin" @selected(old('role')==='admin')>admin</option>
          </select>
          <button class="rounded-xl bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-500 transition">
            Crear
          </button>
        </form>

        {{-- Usuarios --}}
        <h2 class="text-lg font-semibold text-[#0a2342] mb-3">Usuarios registrados</h2>
        <div class="overflow-x-auto">
          <table class="w-full border text-sm rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
              <tr>
                <th class="border px-3 py-2 text-left">ID</th>
                <th class="border px-3 py-2 text-left">Nombre</th>
                <th class="border px-3 py-2 text-left">Email</th>
                <th class="border px-3 py-2 text-left">Rol</th>
                <th class="border px-3 py-2 text-left">Creado</th>
                <th class="border px-3 py-2 text-left">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $u)
                @php $isEditing = (string)request('edit') === (string)$u->id; @endphp
                <tr class="odd:bg-white even:bg-gray-50">
                  <td class="border px-3 py-2 align-top">{{ $u->id }}</td>

                  @if($isEditing)
                    <form method="POST" action="{{ route('admin.users.update', $u) }}">
                      @csrf @method('PUT')
                      <td class="border px-3 py-2 align-top">
                        <input name="name" value="{{ old('name',$u->name) }}" class="border rounded px-2 py-1 w-full" required>
                      </td>
                      <td class="border px-3 py-2 align-top">
                        <input name="email" value="{{ old('email',$u->email) }}" type="email" class="border rounded px-2 py-1 w-full" required>
                      </td>
                      <td class="border px-3 py-2 align-top">
                        <select name="role" class="border rounded px-2 py-1" @if($u->id===auth()->id()) disabled @endif>
                          <option value="user"  @selected(old('role',$u->role)==='user')>user</option>
                          <option value="admin" @selected(old('role',$u->role)==='admin')>admin</option>
                        </select>
                        @if($u->id===auth()->id())
                          <input type="hidden" name="role" value="{{ $u->role }}">
                        @endif
                        <div class="text-xs text-gray-500 mt-1">(Deja vacío para no cambiar contraseña)</div>
                        <input name="password" type="password" class="border rounded px-2 py-1 w-full mt-1"
                               placeholder="Nueva contraseña (opcional)">
                      </td>
                      <td class="border px-3 py-2 align-top">
                        {{ optional($u->created_at)->format('d-m-Y H:i') }}
                      </td>
                      <td class="border px-3 py-2 align-top">
                        <div class="flex items-center gap-2">
                          <button class="px-3 py-1 rounded bg-zinc-900 text-white hover:bg-zinc-700">Guardar</button>
                          <a href="{{ route('admin.users.index') }}" class="px-3 py-1 rounded border hover:bg-gray-50">Cancelar</a>
                        </div>
                      </td>
                    </form>
                  @else
                    <td class="border px-3 py-2 align-top">{{ $u->name }}</td>
                    <td class="border px-3 py-2 align-top">{{ $u->email }}</td>
                    <td class="border px-3 py-2 align-top">{{ $u->role }}</td>
                    <td class="border px-3 py-2 align-top">{{ optional($u->created_at)->format('d-m-Y H:i') }}</td>
                    <td class="border px-3 py-2 align-top">
                      @if($u->id === auth()->id())
                        <span class="text-gray-400">—</span>
                      @else
                        <div class="flex items-center gap-2">
                          <a href="{{ route('admin.users.index', ['edit'=>$u->id]) }}"
                             class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-500">Editar</a>
                          <form method="POST" action="{{ route('admin.users.destroy',$u) }}"
                                onsubmit="return confirm('¿Eliminar este usuario?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-500">Eliminar</button>
                          </form>
                        </div>
                      @endif
                    </td>
                  @endif
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="border px-3 py-6 text-center text-gray-500">No hay usuarios registrados.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</x-app-layout>
