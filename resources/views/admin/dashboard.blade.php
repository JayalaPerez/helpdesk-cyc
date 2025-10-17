<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h1 class="text-2xl font-bold mb-2">Bienvenido, Administrador</h1>
                <p class="mb-6 text-gray-600">
                    Administra usuarios y tickets del sistema Helpdesk.
                </p>

                {{-- Mensajes flash --}}
                @if(session('ok'))
                    <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-2">
                        {{ session('ok') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 rounded-lg bg-red-100 text-red-800 px-4 py-2">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Acciones rápidas --}}
                <div class="mb-6 flex gap-3">
                    <a href="{{ route('tickets.index') }}"
                       class="inline-block px-4 py-2 rounded-xl bg-zinc-900 text-white hover:bg-zinc-700">
                        Gestionar Tickets
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="inline-block px-4 py-2 rounded-xl bg-gray-500 text-white hover:bg-gray-400">
                        Volver al Dashboard
                    </a>
                </div>

                {{-- Tabla de usuarios --}}
                <h2 class="text-lg font-semibold mb-3">Usuarios registrados</h2>
                <div class="overflow-x-auto">
                    <table class="w-full border text-sm">
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
                                <tr class="odd:bg-white even:bg-gray-50">
                                    <td class="border px-3 py-2">{{ $u->id }}</td>
                                    <td class="border px-3 py-2">{{ $u->name }}</td>
                                    <td class="border px-3 py-2">{{ $u->email }}</td>
                                    <td class="border px-3 py-2">{{ $u->role }}</td>
                                    <td class="border px-3 py-2">
                                        {{ optional($u->created_at)->format('d-m-Y H:i') }}
                                    </td>
                                    <td class="border px-3 py-2">
                                        @if($u->id === auth()->id())
                                            <span class="text-gray-500">—</span>
                                        @else
                                            <form method="POST"
                                                  action="{{ route('admin.users.updateRole', $u) }}"
                                                  class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" class="border rounded px-2 py-1">
                                                    <option value="user"  @selected($u->role==='user')>user</option>
                                                    <option value="admin" @selected($u->role==='admin')>admin</option>
                                                </select>
                                                <button
                                                    class="px-3 py-1 rounded bg-zinc-900 text-white hover:bg-zinc-700">
                                                    Guardar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border px-3 py-6 text-center text-gray-500">
                                        No hay usuarios registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
