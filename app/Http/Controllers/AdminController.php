<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminController extends Controller
{
    /**
     * Panel principal de administración.
     * Lista de usuarios y accesos rápidos.
     */
    public function dashboard()
    {
        $users = User::orderBy('id')->get();
        return view('admin.dashboard', compact('users'));
    }
}
