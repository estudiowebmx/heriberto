<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaDenuncia extends Model
{
    protected $fillable = [
        'complainant_id',
        'registered_by',
        'user_id',
        'dia_numero',
        'mes',
        'verificada',
    ];

    public function complainant()
    {
        return $this->belongsTo(Complainant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publicaciones()
    {
        return $this->hasMany(EncuestaPublicacion::class);
    }
}
