<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
            {{ __('Crear Ticket') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">

                <form method="POST"
                      action="{{ route('tickets.store') }}"
                      enctype="multipart/form-data"
                      class="grid gap-4">
                    @csrf

                    {{-- Asunto --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Asunto
                        </label>
                        <input
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            required
                            class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200"
                            placeholder="Ej: No puedo acceder al correo"
                        >
                    </div>

                    {{-- Departamento / Categoría --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Departamento
                            </label>
                            <select
                                name="department"
                                class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200"
                            >
                                <option value="">Seleccione departamento</option>
                                @foreach(config('helpdesk.departments', []) as $d)
                                    <option value="{{ $d }}" @selected(old('department')===$d)>
                                        {{ $d }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Categoría
                            </label>
                            <select
                                name="category"
                                class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200"
                            >
                                <option value="">Seleccione categoría</option>
                                @foreach(config('helpdesk.categories', []) as $c)
                                    <option value="{{ $c }}" @selected(old('category')===$c)>
                                        {{ $c }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Prioridad --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Prioridad
                        </label>
                        <select
                            name="priority"
                            required
                            class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200"
                        >
                            @foreach(config('helpdesk.priorities', []) as $p)
                                <option value="{{ $p }}" @selected(old('priority')===$p)>
                                    {{ $p }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Describe el problema
                        </label>
                        <textarea
                            name="description"
                            rows="4"
                            required
                            class="w-full border rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-200"
                            placeholder="Ej: Desde hoy en la mañana Outlook pide contraseña y no me deja entrar…"
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- Archivo adjunto inicial --}}
                    <div class="grid gap-1">
                        <label class="block text-sm font-medium text-gray-700">
                            Adjuntar archivo (opcional)
                        </label>

                        <input
                            type="file"
                            name="attachment"
                            class="text-sm block"
                        >

                        <p class="text-xs text-gray-500">
                            PDF / Imágenes / DOCX / XLSX · máx 25MB
                        </p>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center gap-3 pt-4">
                        <button
                            class="px-4 py-2 rounded-xl bg-[#0a2342] text-white hover:bg-blue-800 transition">
                            Crear
                        </button>

                        <a href="{{ route('tickets.index') }}"
                           class="px-4 py-2 rounded-xl border hover:bg-gray-50 transition">
                            Volver
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
