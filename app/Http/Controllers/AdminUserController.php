<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
     * Formulario de edici¨®n separado.
     */
    public function edit(User $user)
    {
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
        if ((int) $user->id === (int) auth()->id()) {
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
        $current = auth()->user();

        // Solo un admin puede eliminar usuarios
        if (!$current || !$current->isAdmin()) {
            abort(403);
        }

        // Evitar que un admin se elimine a s¨ª mismo
        if ((int) $user->id === (int) $current->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // Contar relaciones (por seguridad; si realmente no tiene, ser¨¢n 0)
        $ticketsCreados  = $user->ticketsCreated()->count();
        $ticketsAsignado = $user->ticketsAssigned()->count();
        $comentarios     = $user->comments()->count();

        if ($ticketsCreados > 0 || $ticketsAsignado > 0 || $comentarios > 0) {
            $msg = 'No se puede eliminar al usuario porque tiene registros asociados: '
                . $ticketsCreados . ' ticket(s) creados, '
                . $ticketsAsignado . ' ticket(s) asignado(s) y '
                . $comentarios . ' comentario(s).';

            return back()->with('error', $msg);
        }

        try {
            $user->delete();
        } catch (\Throwable $e) {
            Log::error('Error al eliminar usuario en AdminUserController@destroy', [
                'user_to_delete_id' => $user->id,
                'current_user_id'   => $current->id,
                'exception'         => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'No se pudo eliminar el usuario (revisa el log para m¨¢s detalles).'
            );
        }

        return back()->with('ok', 'Usuario eliminado correctamente.');
    }
}

