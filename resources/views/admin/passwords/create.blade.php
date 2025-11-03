<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#0a2342] leading-tight">
            {{ __('Nueva contraseña') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <form method="POST" action="{{ route('admin.passwords.store') }}" class="space-y-6">
                    @csrf

                    {{-- 1. Línea: tipo / aplicación --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select name="tipo"
                                    class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-200"
                                    required>
                                <option value="">-- Seleccione --</option>
                                <option value="Cuenta de correo" {{ old('tipo')=='Cuenta de correo' ? 'selected' : '' }}>Cuenta de correo</option>
                                <option value="Cuenta de aplicación" {{ old('tipo')=='Cuenta de aplicación' ? 'selected' : '' }}>Cuenta de aplicación</option>
                                <option value="Cuenta Bitácora" {{ old('tipo')=='Cuenta Bitácora' ? 'selected' : '' }}>Cuenta Bitácora</option>
                                <option value="Cuenta Helpdesk" {{ old('tipo')=='Cuenta Helpdesk' ? 'selected' : '' }}>Cuenta Helpdesk</option>
                                <option value="Otro" {{ old('tipo')=='Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('tipo')
                                <p class="text-sm text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Aplicación</label>
                            <select name="aplicacion"
                                    class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-200"
                                    required>
                                <option value="">-- Seleccione --</option>
                                <option value="Outlook" {{ old('aplicacion')=='Outlook' ? 'selected' : '' }}>Outlook</option>
                                <option value="Google Drive" {{ old('aplicacion')=='Google Drive' ? 'selected' : '' }}>Google Drive</option>
                                <option value="Brevo" {{ old('aplicacion')=='Brevo' ? 'selected' : '' }}>Brevo</option>
                                <option value="V2Network" {{ old('aplicacion')=='V2Network' ? 'selected' : '' }}>V2Network</option>
                                <option value="Google Correo" {{ old('aplicacion')=='Google Correo' ? 'selected' : '' }}>Google Correo</option>
                                <option value="Aula Virtual" {{ old('aplicacion')=='Aula Virtual' ? 'selected' : '' }}>Aula Virtual</option>
                                <option value="Bitacora C&C" {{ old('aplicacion')=='Cuenta Bitacora' ? 'selected' : '' }}>Bitácora C&C</option>
                                <option value="Otro" {{ old('aplicacion')=='Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('aplicacion')
                                <p class="text-sm text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 2. Línea: estado / usuario --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select name="estado"
                                    class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-200">
                                <option value="Nuevo" {{ old('estado')=='Nuevo' ? 'selected' : '' }}>Nuevo</option>
                                <option value="Eliminado" {{ old('estado')=='Eliminado' ? 'selected' : '' }}>Eliminado</option>
                                <option value="Restringido" {{ old('estado')=='Restringido' ? 'selected' : '' }}>Restringido</option>
                            </select>
                            @error('estado')
                                <p class="text-sm text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                            <input type="text" name="usuario"
                                   value="{{ old('usuario', auth()->user()->name) }}"
                                   class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-200">
                            @error('usuario')
                                <p class="text-sm text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 3. Línea: correo / contraseña --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                            <input type="email" name="correo"
                                   value="{{ old('correo') }}"
                                   class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-200"
                                   placeholder="usuario@consultorescyc.cl">
                            @error('correo')
                                <p class="text-sm text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                            <input type="text" name="password"
                                   value="{{ old('password') }}"
                                   class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-200"
                                   placeholder="Prueba123+"
                                   required>
                            @error('password')
                                <p class="text-sm text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- 4. Línea: fechas (solo info) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de creación</label>
                            <input type="text"
                                   class="w-full rounded-xl border-gray-200 bg-gray-100"
                                   value="{{ now()->format('d-m-Y') }}"
                                   readonly>
                            <p class="text-[11px] text-gray-400 mt-1">Se guarda automáticamente.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de eliminación</label>
                            <input type="text"
                                   class="w-full rounded-xl border-gray-200 bg-gray-100"
                                   value="—"
                                   readonly>
                            <p class="text-[11px] text-gray-400 mt-1">Solo se llenará cuando se marque como eliminada.</p>
                        </div>
                    </div>

                    {{-- 5. Observaciones --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="4"
                                  class="w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-blue-200"
                                  placeholder="Creación de cuenta de prueba, cambio de hosting, etc.">{{ old('observaciones') }}</textarea>
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('admin.passwords.index') }}"
                           class="px-4 py-2 rounded-xl border hover:bg-gray-50">Volver</a>
                        <button class="px-5 py-2 rounded-xl bg-[#0D3B66] text-white hover:bg-[#0b3054]">
                            Guardar
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
