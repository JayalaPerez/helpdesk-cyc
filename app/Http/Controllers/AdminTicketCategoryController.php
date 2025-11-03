<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use Illuminate\Http\Request;

class AdminTicketCategoryController extends Controller
{
    public function index()
    {
        $categories = TicketCategory::orderBy('name')->get();

        return view('admin.ticket-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255','unique:ticket_categories,name'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        TicketCategory::create($data);

        return back()->with('ok', 'Categoría creada.');
    }

    public function update(Request $request, TicketCategory $ticketCategory)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255','unique:ticket_categories,name,'.$ticketCategory->id],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $ticketCategory->update($data);

        return back()->with('ok', 'Categoría actualizada.');
    }

    public function destroy(TicketCategory $ticketCategory)
    {
        $ticketCategory->delete();

        return back()->with('ok', 'Categoría eliminada.');
    }
}
