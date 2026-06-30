<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lona extends Model
{
    protected $table = 'lonas';

    protected $fillable = [
        'denunciante_nombre',
        'denunciante_clave_elector',
        'denunciante_domicilio',
        'denunciante_email',
        'denunciante_sexo',
        'registered_by',
        'user_id',
        'dia_numero',
        'mes',
        'verificada',
    ];

    public function evidences()
    {
        return $this->hasMany(LonaEvidence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
