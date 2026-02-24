<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'blood_type_id',
        'allergies',
        'chronic_diseases',
        'surgery_history',
        'family_history',
        'observations',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_relationship',
    ];

    //Relación uno a uno inversa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Relación muchos a uno con BloodType
    public function bloodType()
    {
        return $this->belongsTo(BloodType::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }
}
