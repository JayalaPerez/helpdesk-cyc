<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Guarda un comentario nuevo en un ticket,
     * con adjunto opcional.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $user = auth()->user();

        // Mismo criterio que en TicketController@show:
        $esAdmin    = $user->isAdmin();
        $esDueno    = ((int) $ticket->user_id === (int) $user->id);
        $esAsignado = ($ticket->assigned_user_id && (int) $ticket->assigned_user_id === (int) $user->id);

        // Si no es admin, ni dueño, ni asignado → 403
        if (!$esAdmin && !$esDueno && !$esAsignado) {
            abort(403);
        }

        $data = $request->validate([
            'body'       => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:2048'], // máx 2MB
        ]);

        $path         = null;
        $originalName = null;

        if ($request->hasFile('attachment')) {
            $file         = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            // Guardamos en storage/app/public/ticket_attachments/{ticket_id}/...
            $path = $file->store('ticket_attachments/'.$ticket->id, 'public');
        }

        Comment::create([
            'ticket_id'       => $ticket->id,
            'user_id'         => auth()->id(),
            'body'            => $data['body'],
            'attachment_path' => $path,
            'attachment_name' => $originalName,
        ]);

        return back()->with('ok', 'Comentario agregado.');
    }

    /**
     * Descarga el adjunto asociado a un comentario.
     */
    public function download(Comment $comment)
    {
        $user   = auth()->user();
        $ticket = $comment->ticket; // relación comment -> ticket

        $esAdmin            = $user->isAdmin();
        $esDueno            = $ticket && (int) $ticket->user_id === (int) $user->id;
        $esAsignado         = $ticket && $ticket->assigned_user_id
                              && (int) $ticket->assigned_user_id === (int) $user->id;
        $esAutorComentario  = (int) $comment->user_id === (int) $user->id;

        // Puede descargar: admin, dueño del ticket, asignado o autor del comentario
        if (!$esAdmin && !$esDueno && !$esAsignado && !$esAutorComentario) {
            abort(403);
        }

        // Si el comentario no tiene archivo, 404.
        if (!$comment->attachment_path) {
            abort(404, 'Archivo no disponible.');
        }

        // Verificamos si existe en el disco 'public'
        if (!Storage::disk('public')->exists($comment->attachment_path)) {
            abort(404, 'El archivo no se encontró en el servidor.');
        }

        // Nombre bonito al descargar (cae a nombre del archivo si viene null)
        $downloadName = $comment->attachment_name ?: basename($comment->attachment_path);

        return Storage::disk('public')->download(
            $comment->attachment_path,
            $downloadName
        );
    }
}

