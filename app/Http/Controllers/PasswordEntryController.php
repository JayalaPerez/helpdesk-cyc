<?php

namespace App\Http\Controllers;

use App\Models\PasswordEntry;
use Illuminate\Http\Request;

class PasswordEntryController extends Controller
{
    public function index(Request $request)
    {
        // con filtros
        $query = PasswordEntry::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($query) use ($q) {
                $query->where('aplicacion', 'like', "%$q%")
                    ->orWhere('usuario', 'like', "%$q%")
                    ->orWhere('correo', 'like', "%$q%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        $entries = $query->orderByDesc('id')->paginate(10);

        return view('admin.passwords.index', compact('entries'));
    }

    public function create()
    {
        return view('admin.passwords.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'          => 'required|string|max:255',
            'aplicacion'    => 'required|string|max:255',
            'estado'        => 'required|string|max:50',
            'usuario'       => 'nullable|string|max:255',
            'correo'        => 'nullable|email|max:255',
            'password'      => 'required|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $data['fecha_creacion']    = now()->toDateString();
        $data['fecha_eliminacion'] = null;

        if (empty($data['usuario'])) {
            $data['usuario'] = auth()->user()->name ?? auth()->user()->email;
        }

        $entry = new PasswordEntry($data);
        // dispara el mutator
        $entry->password = $data['password'];
        $entry->save();

        return redirect()
            ->route('admin.passwords.index')
            ->with('ok', 'Contraseña registrada');
    }

    public function edit(PasswordEntry $password)
    {
        return view('admin.passwords.edit', ['entry' => $password]);
    }

    public function update(Request $request, PasswordEntry $password)
    {
        $data = $request->validate([
            'tipo'              => ['required','string','max:255'],
            'aplicacion'        => ['required','string','max:255'],
            'estado'            => ['required','string','max:255'],
            'usuario'           => ['nullable','string','max:255'],
            'correo'            => ['nullable','string','max:255'],
            'password'          => ['nullable','string','max:1024'],
            'fecha_creacion'    => ['nullable','date'],
            'fecha_eliminacion' => ['nullable','date'],
            'observaciones'     => ['nullable','string'],
        ]);

        $password->fill($data);

        if ($request->filled('password')) {
            $password->password = $data['password']; // re-cifrar
        }

        $password->save();

        return redirect()->route('admin.passwords.index')->with('ok','Contraseña actualizada');
    }

    public function destroy(PasswordEntry $password)
    {
        // aquí sí borramos de verdad
        $password->delete();

        return redirect()
            ->route('admin.passwords.index')
            ->with('ok', 'Contraseña eliminada definitivamente.');
    }

    // 👇 NUEVO: marcar como "De baja"
    public function darDeBaja(PasswordEntry $password)
    {
        $obs = trim(($password->observaciones ?? '') . "\nDada de baja por: " . (auth()->user()->name ?? auth()->user()->email) . ' el ' . now()->format('d-m-Y H:i'));

        $password->update([
            'estado'           => 'De baja',
            'fecha_eliminacion'=> now()->toDateString(),
            'observaciones'    => $obs,
            'eliminado_por'    => auth()->user()->name ?? auth()->user()->email, // si creaste la columna
        ]);

        return back()->with('ok', 'La cuenta fue marcada como "De baja".');
    }
}
