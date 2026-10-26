<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';

    protected $fillable = ['nombre'];

    public function personas()
    {
        return $this->belongsToMany(Persona::class, 'grupo_persona');
    }
}
