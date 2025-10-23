<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $guarded = [];

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
