<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index()
    {
        $query = Ticket::with(['requester','assignee'])->latest();

        // 🔒 Si NO es admin, solo sus propios tickets
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if (request('status'))   $query->where('status', request('status'));
        if (request('priority')) $query->where('priority', request('priority'));
        if (request('q')) {
            $q = '%'.request('q').'%';
            $query->where(function($s) use ($q) {
                $s->where('subject', 'like', $q)
                    ->orWhere('description', 'like', $q)
                    ->orWhere('department', 'like', $q)
                    ->orWhere('category', 'like', $q)
                    ->orWhereHas('requester', function($sub) use ($q) {
                        $sub->where('name', 'like', $q)
                            ->orWhere('email', 'like', $q);
                })
                ->orWhereHas('assignee', function($sub) use ($q) {
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
        // Lee desde config/helpdesk.php si existe; si no, usa los valores por defecto
        $departments = config('helpdesk.departments', [
            'Marketing','RRHH','Administración','Informática','Gerencia',
            'Psicología','Control de gestión','Diseño','Comercial',
        ]);

        $categories = config('helpdesk.categories', [
            'Correo','Aplicación','Hardware','Software','Otro',
            'Creación de Usuario','Eliminación de Usuario',
        ]);

        $priorities = config('helpdesk.priorities', ['Baja','Media','Alta','Crítica']);

        return view('tickets.create', compact('departments','categories','priorities'));
    }

    public function store(Request $request)
    {
        $departments = config('helpdesk.departments', []);
        $categories  = config('helpdesk.categories', []);
        $priorities  = config('helpdesk.priorities', []);

        // Validamos campos del ticket + archivo opcional
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'department'  => ['nullable','string','max:255'],
            'priority'    => ['required', Rule::in($priorities)],
            'description' => 'required|string',
            'category'    => ['nullable','string','max:255'],
            'attachment'  => ['nullable', 'file', 'max:25600','mimes:pdf,jpg,jpeg,png,doc,docx,xlsx,zip,eml,msg','mimetypes:application/pdf,image/jpeg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,message/rfc822'], // 25MB
        ]);

        // Normalizamos vacíos
        $data['department'] = $data['department'] ?: null;
        $data['category']   = $data['category']   ?: null;

        // Campos del ticket
        $data['user_id'] = auth()->id();
        $data['status']  = 'Nuevo'; // siempre arranca en Nuevo

        // Creamos el ticket
        $ticket = Ticket::create($data);

        //
        // Si viene archivo, lo subimos y creamos comentario inicial
        //
        if ($request->hasFile('attachment')) {
            $file          = $request->file('attachment');
            $originalName  = $file->getClientOriginalName();
            $storedPath    = $file->store('ticket_attachments/'.$ticket->id, 'public');

            // Creamos comentario "sistema" con el adjunto como si fuera el usuario
            \App\Models\Comment::create([
                'ticket_id'        => $ticket->id,
                'user_id'          => auth()->id(),
                'body'             => $data['description'], // usamos la misma descripción inicial
                'attachment_path'  => $storedPath,
                'attachment_name'  => $originalName,
            ]);

        } else {
            // Si NO hubo adjunto, igual queremos guardar la descripción
            // como primer comentario visible para histórico:
            \App\Models\Comment::create([
                'ticket_id'        => $ticket->id,
                'user_id'          => auth()->id(),
                'body'             => $data['description'],
                'attachment_path'  => null,
                'attachment_name'  => null,
            ]);        
        }

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('ok','Ticket creado');
    }

    public function show(Ticket $ticket)
    {
        // 🔒 usuarios normales no pueden ver tickets ajenos
        if (!auth()->user()->isAdmin() && $ticket->user_id !== auth()->id()) {
            abort(403);
        }

        // Cargar relaciones necesarias (requester, comentarios y asignado)
        $ticket->load(['requester','comments.author','assignee']);

        // Solo administradores para el combo de asignación
        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->get(['id','name','email']);

        return view('tickets.show', compact('ticket', 'admins'));
    }

    public function edit(Ticket $ticket)
    {
        if (auth()->id() !== $ticket->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $departments = config('helpdesk.departments', [
            'Marketing','RRHH','Administración','Informática','Gerencia',
            'Psicología','Control de gestión','Diseño','Comercial',
        ]);

        $categories = config('helpdesk.categories', [
            'Correo','Aplicación','Hardware','Software','Otro',
            'Ingreso de Usuario','Eliminación de Usuario',
        ]);

        $priorities = config('helpdesk.priorities', ['Baja','Media','Alta','Crítica']);

        return view('tickets.edit', compact('ticket','departments','categories','priorities'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        // 🔒 Solo el dueño o un admin puede editar
        if (auth()->id() !== $ticket->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }
    

        // 🚫 Evitar edición si está cerrado y no es admin
        if (!auth()->user()->isAdmin() && $ticket->status === 'Cerrado') {
            return redirect()->back()->with('error', 'Este ticket ya está cerrado y no puede ser editado.');
        }


        $departments = config('helpdesk.departments', []);
        $categories  = config('helpdesk.categories', []);
        $priorities  = config('helpdesk.priorities', ['Baja','Media','Alta','Crítica']);
        $statuses    = config('helpdesk.statuses', ['Nuevo','En Progreso','Resuelto','Cerrado']);

        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'department'  => ['nullable','string','max:255', Rule::in($departments)],
            'priority'    => ['required', Rule::in($priorities)],
            'description' => 'required|string',
            'category'    => ['nullable','string','max:255', Rule::in($categories)],
        ]);

        $data['department'] = $data['department'] ?: null;
        $data['category']   = $data['category']   ?: null;

        // Solo admin puede cambiar estado
        if (auth()->user()->isAdmin() && $request->filled('status')) {
            $request->validate([
                'status' => ['required', Rule::in($statuses)],
            ]);
            $data['status'] = $request->status;
        }

        $ticket->fill($data);
        $ticket->category = $data['category'];
        $ticket->save();

        return redirect()->route('tickets.show', $ticket)->with('ok','Ticket actualizado');
    }


    public function destroy(Ticket $ticket)
    {
        // Solo admin puede hacer esto
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No autorizado');
        }

        // 1. Registrar auditoría ANTES de borrar nada
        \App\Models\TicketAudit::create([
            'ticket_id'          => $ticket->id,
            'deleted_by'         => auth()->id(),

            'subject'            => $ticket->subject,
            'description'        => $ticket->description,
            'department'         => $ticket->department,
            'category'           => $ticket->category,
            'priority'           => $ticket->priority,
            'status'             => $ticket->status,

            'user_id'            => $ticket->user_id,             // creador
            'assigned_user_id'   => $ticket->assigned_user_id,    // asignado actual

            'ticket_created_at'  => $ticket->created_at,
            'ticket_closed_at'   => $ticket->closed_at,

            'deleted_at'         => now(),
        ]);

        // 2. Borrar comentarios asociados
        //    También podemos (opcionalmente) limpiar los archivos adjuntos físicos
        foreach ($ticket->comments as $comment) {
            if ($comment->attachment_path) {
                // Borra el archivo físico desde storage/app/public/...
                \Illuminate\Support\Facades\Storage::disk('public')
                    ->delete($comment->attachment_path);
            }
            $comment->delete();
        }

        // 3. (Opcional) Si quieres además borrar todo el directorio del ticket:
        // Storage::disk('public')->deleteDirectory('ticket_attachments/'.$ticket->id);

        // 4. Finalmente borramos el ticket
        $ticket->delete();

        // 5. Volver con confirmación
        return back()->with('ok', 'Ticket eliminado correctamente y registrado en auditoría.');
    }



    // ✅ Solo Admin: actualizar estado
    public function updateStatus(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $statuses = config('helpdesk.statuses', ['Nuevo','En Progreso','Resuelto','Cerrado']);

        $data = $request->validate([
            'status' => ['required', Rule::in($statuses)],
        ]);

        $ticket->status = $data['status'];

        // setear closed_at si se cierra
        $ticket->closed_at = ($data['status'] === 'Cerrado') ? now() : null;
        $ticket->save();

        return back()->with('ok', 'Estado actualizado');
    }

    // ✅ Solo Admin: asignar a otro usuario/admin
    public function assign(Request $request, Ticket $ticket)
    {
        if (!auth()->user()->isAdmin()) abort(403);

        $data = $request->validate([
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);

        $ticket->assigned_user_id = $data['assigned_user_id'] ?? null;
        $ticket->save();

        return back()->with('ok', 'Ticket asignado');
    }

    public function adminUpdate(Request $request, Ticket $ticket)
    {
        // solo admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $statuses = config('helpdesk.statuses', ['Nuevo','En Progreso','Resuelto','Cerrado']);

        $data = $request->validate([
            'status'           => ['required', Rule::in($statuses)],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ]);

        // actualizar estado
        $ticket->status = $data['status'];

        // si se cierra, ponemos fecha; si no, la limpiamos
        $ticket->closed_at = ($data['status'] === 'Cerrado') ? now() : null;

        // actualizar asignación
        $ticket->assigned_user_id = $data['assigned_user_id'] ?? null;

        $ticket->save();

        return back()->with('ok', 'Ticket actualizado');
    }

}
