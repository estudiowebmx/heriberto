<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complainant extends Model
{
    protected $fillable = [
        'nombre_completo',
        'clave_elector',
        'domicilio',
        'email',
        'sexo',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function lonas()
    {
        return $this->hasMany(Lona::class);
    }
}
