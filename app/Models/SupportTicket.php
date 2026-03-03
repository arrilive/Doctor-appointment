<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Modelo para los tickets de soporte
class SupportTicket extends Model
{
    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
    ];

    // Un ticket pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
