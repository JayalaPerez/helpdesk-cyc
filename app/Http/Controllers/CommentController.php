<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $request->validate(['body'=>'required|string']);
        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);
        return back()->with('ok','Comentario agregado');
    }
}
