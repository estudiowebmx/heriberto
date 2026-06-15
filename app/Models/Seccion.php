<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'secciones';

    protected $fillable = ['seccion', 'municipio', 'distrito_local', 'distrito_federal', 'demarcacion', 'meta'];
}
