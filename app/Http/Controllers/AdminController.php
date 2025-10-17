<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
    }
    public function updateUserRole(\Illuminate\Http\Request $request, \App\Models\User $user)
    {
        // Evitar que te cambies a ti mismo por seguridad
        if ($user->id === auth()->id()) {
        return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $data = $request->validate([
        'role' => ['required', 'in:admin,user'],
        ]);

        $user->update(['role' => $data['role']]);

        return back()->with('ok', "Rol actualizado: {$user->email} ahora es {$data['role']}.");
    }
}
