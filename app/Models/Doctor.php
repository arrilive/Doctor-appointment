<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'speciality_id',
        'medical_license_number',
        'biography',
    ];
    // Un doctor pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Un doctor tiene una especialidad
    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }
}
