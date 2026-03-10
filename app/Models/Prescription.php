<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = ['consultation_id', 'medication', 'dosage', 'frequency'];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
