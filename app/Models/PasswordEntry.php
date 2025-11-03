<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PasswordEntry extends Model
{
    protected $table = 'password_entries';

    protected $fillable = [
        'tipo',
        'aplicacion',
        'estado',
        'usuario',
        'correo',
        'password_encrypted',
        'fecha_creacion',
        'fecha_eliminacion',
        'observaciones',
    ];

    // guarda cifrado
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_encrypted'] = Crypt::encryptString($value);
    }

    // devuelve descifrado al pedir $entry->password
    public function getPasswordAttribute()
    {
        if (!isset($this->attributes['password_encrypted'])) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['password_encrypted']);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // alias para la vista: $entry->password_decrypted
    public function getPasswordDecryptedAttribute()
    {
        return $this->password;
    }
}
