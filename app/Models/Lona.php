<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lona extends Model
{
    protected $table = 'lonas';

    protected $fillable = [
        'complainant_id',
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

    public function complainant()
    {
        return $this->belongsTo(Complainant::class);
    }

    public function evidences()
    {
        return $this->hasMany(LonaEvidence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
