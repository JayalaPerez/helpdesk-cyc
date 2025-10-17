<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $query = Ticket::with('requester')->latest();

        if (request('status'))   $query->where('status', request('status'));
        if (request('priority')) $query->where('priority', request('priority'));
        if (request('q')) {
            $q = '%'.request('q').'%';
            $query->where(function($s) use ($q){
                $s->where('subject','like',$q)
                  ->orWhere('description','like',$q)
                  ->orWhere('department','like',$q);
            });
        }

        $tickets = $query->paginate(12)->withQueryString();
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'=>'required|string|max:255',
            'department'=>'nullable|string|max:255',
            'priority'=>'required|in:Baja,Media,Alta,Crítica',
            'status'=>'required|in:Nuevo,En Progreso,Resuelto,Cerrado',
            'description'=>'required|string',
        ]);

        $data['user_id'] = auth()->id();
        $ticket = Ticket::create($data);

        return redirect()->route('tickets.show', $ticket)->with('ok','Ticket creado');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['requester','comments.author']);
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        if (auth()->id() !== $ticket->user_id && !auth()->user()->isAdmin()) abort(403);
        return view('tickets.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        if (auth()->id() !== $ticket->user_id && !auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'subject'=>'required|string|max:255',
            'department'=>'nullable|string|max:255',
            'priority'=>'required|in:Baja,Media,Alta,Crítica',
            'status'=>'required|in:Nuevo,En Progreso,Resuelto,Cerrado',
            'description'=>'required|string',
        ]);

        $ticket->update($data);
        return redirect()->route('tickets.show',$ticket)->with('ok','Ticket actualizado');
    }

    public function destroy(Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) abort(403);
        $ticket->delete();
        return redirect()->route('tickets.index')->with('ok','Ticket eliminado');
    }
}
