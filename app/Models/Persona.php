<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'telefono',
        'seccion',
        'municipio',
        'distrito_local',
        'distrito_federal',
        'demarcacion',
    ];
}
