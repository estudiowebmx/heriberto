<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = 'encuestas';

    protected $fillable = [
        'nombre_completo',
        'fecha_nacimiento',
        'telefono',
        'profesion_ocupacion',
        'lugar_nacimiento',
        'domicilio_actual',
        'hijos_info',
        'padres_viven',
        'padres_origen',
        'padres_ocupacion',
        'preocupaciones',
        'problema_justicia',
        'enfoque_gobernador',
        'errores_gobierno',
        'deficiencias_servicios',
    ];

    protected function casts(): array
    {
        return [
            'preocupaciones' => 'array',
            'errores_gobierno' => 'array',
            'deficiencias_servicios' => 'array',
            'fecha_nacimiento' => 'date',
        ];
    }
}
