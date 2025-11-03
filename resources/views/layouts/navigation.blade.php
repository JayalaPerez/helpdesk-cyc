<nav class="bg-[#0D3B66] text-white shadow">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex h-14 items-center justify-between">

      {{-- Izquierda: Logo + título --}}
      <div class="flex items-center gap-3">
        <a href="https://consultorescyc.cl" target="_blank" class="flex items-center gap-2">
          <img src="{{ asset('images/Logocyccorreos.png') }}" alt="C&C" class="h-10 w-auto">
          <span class="font-bold text-lg tracking-wide text-white">Helpdesk C&C</span>
        </a>
      </div>

      {{-- Centro: menú principal --}}
      <div class="hidden md:flex items-center gap-6">
        <a href="{{ route('dashboard') }}"
           class="hover:text-zinc-200 @if(request()->routeIs('dashboard')) underline @endif">
          Dashboard
        </a>

        <a href="{{ route('tickets.index') }}"
           class="hover:text-zinc-200 @if(request()->routeIs('tickets.*')) underline @endif">
          Tickets
        </a>

        @if(auth()->user()?->isAdmin())
          {{-- SOLO se subraya en /admin o /admin/dashboard --}}
          <a href="{{ route('admin.dashboard') }}"
             class="hover:text-zinc-200 @if(request()->routeIs('admin.dashboard')) underline @endif">
            Panel Admin
          </a>
        @endif
        
      </div>

      {{-- Derecha: usuario --}}
      <div class="flex items-center gap-3">
        <x-dropdown align="right" width="48">
          <x-slot name="trigger">
            <button class="inline-flex items-center rounded-md px-3 py-2 hover:bg-white/10">
              <span class="mr-2">{{ auth()->user()->name }}</span>
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                      d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                      clip-rule="evenodd" />
              </svg>
            </button>
          </x-slot>

          <x-slot name="content">
            <x-dropdown-link href="{{ route('profile.edit') }}">
              Perfil
            </x-dropdown-link>

            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <x-dropdown-link href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                Cerrar sesión
              </x-dropdown-link>
            </form>
          </x-slot>
        </x-dropdown>
      </div>
    </div>
  </div>
</nav>
