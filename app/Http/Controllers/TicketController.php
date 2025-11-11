<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $query = Ticket::with(['requester', 'assignee'])->latest();

        // si NO es admin, solo sus propios tickets
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        // filtros
        if (request('status')) {
            $query->where('status', request('status'));
        }
        if (request('priority')) {
            $query->where('priority', request('priority'));
        }
        if (request('q')) {
            $q = '%'.request('q').'%';
            $query->where(function ($s) use ($q) {
                $s->where('subject', 'like', $q)
                    ->orWhere('description', 'like', $q)
                    ->orWhere('department', 'like', $q)
                    ->orWhere('category', 'like', $q)
                    ->orWhereHas('requester', function ($sub) use ($q) {
                        $sub->where('name', 'like', $q)
                            ->orWhere('email', 'like', $q);
                    })
                    ->orWhereHas('assignee', function ($sub) use ($q) {
                        $sub->where('name', 'like', $q)
                            ->orWhere('email', 'like', $q);
                    });
            });
        }

        $tickets = $query->paginate(12)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $departments = Department::where('is_active', 1)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $categories = TicketCategory::where('is_active', 1)
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        // si no hay en config, usamos estas
        $priorities = config('helpdesk.priorities', ['Baja', 'Media', 'Alta', 'Crítica']);

        return view('tickets.create', compact('departments', 'categories', 'priorities'));
    }

    public function store(Request $request)
    {
        $departments = Department::where('is_active', 1)->pluck('name')->toArray();
        $categories  = TicketCategory::where('is_active', 1)->pluck('name')->toArray();
        $priorities  = config('helpdesk.priorities', ['Baja', 'Media', 'Alta', 'Crítica']);

        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'department'  => ['nullable', 'string', 'max:255', Rule::in($departments)],
            'priority'    => ['required', Rule::in($priorities)],
            'description' => 'required|string',
            'category'    => ['nullable', 'string', 'max:255', Rule::in($categories)],
            'attachment'  => [
                'nullable',
                'file',
                'max:25600',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xlsx,zip,eml,msg',
                'mimetypes:application/pdf,image/jpeg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,message/rfc822',
            ],
        ]);

        // normalizar
        $data['department'] = $data['department'] ?: null;
        $data['category']   = $data['category']   ?: null;
        $data['user_id']    = auth()->id();
        $data['status']     = 'Nuevo';

        $ticket = Ticket::create($data);

        // comentario inicial (con o sin archivo)
        if ($request->hasFile('attachment')) {
            $file         = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $storedPath   = $file->store('ticket_attachments/'.$ticket->id, 'public');

            \App\Models\Comment::create([
                'ticket_id'       => $ticket->id,
                'user_id'         => auth()->id(),
                'body'            => $data['description'],
                'attachment_path' => $storedPath,
                'attachment_name' => $originalName,
            ]);
        } else {
            \App\Models\Comment::create([
                'ticket_id' => $ticket->id,
                'user_id'   => auth()->id(),
                'body'      => $data['description'],
            ]);
        }

        // IMPORTANTE: devolver al show del ticket recién creado
        return redirect()
            ->route('tickets.show', $ticket)
            ->with('ok', 'Ticket creado correctamente');
    }

    public function show(Ticket $ticket)
    {
        $user = auth()->user();

        $esAdmin    = $user->isAdmin();
        $esDueno    = ((int) $ticket->user_id === (int) $user->id);
        $esAsignado = ($ticket->assigned_user_id && (int) $ticket->assigned_user_id === (int) $user->id);

        // si no es admin, ni dueño, ni asignado → 403
        if (!$esAdmin && !$esDueno && !$esAsignado) {
            abort(403);
        }

        $ticket->load(['requester', 'comments.author', 'assignee']);

        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('tickets.show', compact('ticket', 'admins'));
    }

    public function edit(Ticket $ticket)
    {
        if (auth()->id() !== $ticket->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $departments = Department::where('is_active', 1)->pluck('name')->toArray();
        $categories  = TicketCategory::where('is_active', 1)->pluck('name')->toArray();
        $priorities  = config('helpdesk.priorities', ['Baja', 'Media', 'Alta', 'Crítica']);

        return view('tickets.edit', compact('ticket', 'departments', 'categories', 'priorities'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        if (auth()->id() !== $ticket->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        // si no es admin y está cerrado, no deja
        if (!auth()->user()->isAdmin() && $ticket->status === 'Cerrado') {
            return back()->with('error', 'Este ticket ya está cerrado y no puede ser editado.');
        }

        $departments = Department::where('is_active', 1)->pluck('name')->toArray();
        $categories  = TicketCategory::where('is_active', 1)->pluck('name')->toArray();
        $priorities  = config('helpdesk.priorities', ['Baja', 'Media', 'Alta', 'Crítica']);
        $statuses    = config('helpdesk.statuses', ['Nuevo', 'En Progreso', 'Resuelto', 'Cerrado']);

        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'department'  => ['nullable', 'string', 'max:255', Rule::in($departments)],
            'priority'    => ['required', Rule::in($priorities)],
            'description' => 'required|string',
            'category'    => ['nullable', 'string', 'max:255', Rule::in($categories)],
        ]);

        $data['department'] = $data['department'] ?: null;
        $data['category']   = $data['category']   ?: null;

        // solo admin puede cambiar estado
        if (auth()->user()->isAdmin() && $request->filled('status')) {
            $request->validate([
                'status' => ['required', Rule::in($statuses)],
            ]);
            $data['status'] = $request->status;
        }

        $ticket->fill($data)->save();

        return redirect()->route('tickets.show', $ticket)->with('ok', 'Ticket actualizado');
    }

    public function destroy(Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No autorizado');
        }

        \App\Models\TicketAudit::create([
            'ticket_id'          => $ticket->id,
            'deleted_by'         => auth()->id(),
            'subject'            => $ticket->subject,
            'description'        => $ticket->description,
            'department'         => $ticket->department,
            'category'           => $ticket->category,
            'priority'           => $ticket->priority,
            'status'             => $ticket->status,
            'user_id'            => $ticket->user_id,
            'assigned_user_id'   => $ticket->assigned_user_id,
            'ticket_created_at'  => $ticket->created_at,
            'ticket_closed_at'   => $ticket->closed_at,
            'deleted_at'         => now(),
        ]);

        foreach ($ticket->comments as $comment) {
            if ($comment->attachment_path) {
                Storage::disk('public')->delete($comment->attachment_path);
            }
            $comment->delete();
        }

        $ticket->delete();

        return back()->with('ok', 'Ticket eliminado correctamente y registrado en auditoría.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $statuses = config('helpdesk.statuses', ['Nuevo', 'En Progreso', 'Resuelto', 'Cerrado']);

        $data = $request->validate([
            'status' => ['required', Rule::in($statuses)],
        ]);

        $ticket->status = $data['status'];
        $ticket->closed_at = ($data['status'] === 'Cerrado') ? now() : null;
        $ticket->save();

        return back()->with('ok', 'Estado actualizado');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $ticket->assigned_user_id = $data['assigned_user_id'] ?? null;
        $ticket->save();

        return back()->with('ok', 'Ticket asignado');
    }

    public function adminUpdate(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $statuses = config('helpdesk.statuses', ['Nuevo', 'En Progreso', 'Resuelto', 'Cerrado']);

        $data = $request->validate([
            'status'           => ['required', Rule::in($statuses)],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $ticket->status = $data['status'];
        $ticket->closed_at = ($data['status'] === 'Cerrado') ? now() : null;
        $ticket->assigned_user_id = $data['assigned_user_id'] ?? null;
        $ticket->save();

        return back()->with('ok', 'Ticket actualizado');
    }
}

