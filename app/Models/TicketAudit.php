<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAudit extends Model
{
    protected $fillable = [
        'ticket_id',
        'deleted_by',
        'subject',
        'description',
        'department',
        'category',
        'priority',
        'status',
        'user_id',
        'assigned_user_id',
        'ticket_created_at',
        'ticket_closed_at',
        'deleted_at',
    ];

    protected $casts = [
        'ticket_created_at' => 'datetime',
        'ticket_closed_at'  => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    // Opcional: relaciones de conveniencia
    public function admin()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
