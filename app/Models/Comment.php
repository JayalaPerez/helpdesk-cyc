<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'body',
        'attachment_path',
        'attachment_name',
    ];

    // quién escribió el comentario
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // a qué ticket pertenece
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // URL pública del adjunto
    public function attachmentUrl(): ?string
    {
        if (!$this->attachment_path) return null;
        return asset('storage/'.$this->attachment_path);
    }
}
