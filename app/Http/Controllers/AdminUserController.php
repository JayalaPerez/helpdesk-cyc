<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Lista de usuarios (se usa en el panel admin).
     */
    public function index()
    {
        $users = User::orderBy('id')->get();
        return view('admin.dashboard', compact('users'));
    }

    /**
     * Crear usuario desde el formulario del panel admin.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', Rule::in(['admin','user'])],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        return back()->with('ok', 'Usuario creado correctamente.');
    }

    /**
     * Formulario de edición separado.
     */
    public function edit(User $user)
    {
        // Si no tienes una vista aparte, esto dará error.
        // Puedes crear resources/views/admin/users/edit.blade.php
        // o simplemente redirigir al dashboard.
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Guardar cambios del usuario.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin','user'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // Evitar que el admin cambie su propio rol
        if ($user->id === auth()->id()) {
            unset($data['role']);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.dashboard')->with('ok', 'Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return back()->with('ok', 'Usuario eliminado correctamente.');
    }
}
