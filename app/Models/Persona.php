<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
