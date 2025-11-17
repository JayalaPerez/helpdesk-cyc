<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Verifica si el usuario tiene rol de administrador.
     */
    public function isAdmin(): bool
    {
        // IMPORTANTE: la BD debe tener exactamente 'admin'
        return $this->role === 'admin';
    }

    /**
     * Nombre visible del usuario (nombre si existe, o email como respaldo).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->email;
    }

    /**
     * Tickets creados por este usuario.
     */
    public function ticketsCreated()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'user_id');
    }

    /**
     * Tickets asignados a este usuario.
     */
    public function ticketsAssigned()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'assigned_user_id');
    }

    /**
     * Comentarios hechos por este usuario.
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class, 'user_id');
    }
}
