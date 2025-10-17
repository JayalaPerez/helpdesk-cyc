<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="mb-4">{{ __("You're logged in!") }}</p>

                {{-- Botón para tickets (visible para todos los usuarios) --}}
                <a href="{{ route('tickets.index') }}" 
                   class="inline-block px-4 py-2 rounded-xl bg-zinc-900 text-white hover:bg-zinc-700 mr-2">
                    Ir a Tickets
                </a>

                {{-- Solo para admin --}}
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-block px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-500">
                        Panel Admin
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
