<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemostracionPersona extends Model
{
    protected $table = 'demostracion_personas';

    protected $fillable = [
        'nombre',
        'telefono',
    ];
}
