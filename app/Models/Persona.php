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
        'sexo',
        'fecha_nacimiento',
        'celular',
        'curp',
        'clave_elector',
        'domicilio',
        'colonia',
        'localidad',
        'seccion',
        'promotor',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date:Y-m-d',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_persona');
    }

    public function seccionInfo()
    {
        return $this->belongsTo(Seccion::class, 'seccion', 'seccion');
    }
}
