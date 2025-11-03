<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class AdminDepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255','unique:departments,name'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Department::create($data);

        return back()->with('ok', 'Departamento creado.');
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255','unique:departments,name,'.$department->id],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $department->update($data);

        return back()->with('ok', 'Departamento actualizado.');
    }

    public function destroy(Department $department)
    {
        // si quieres evitar borrar porque hay tickets, puedes cambiarlo a inactivar
        $department->delete();

        return back()->with('ok', 'Departamento eliminado.');
    }
}
