<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncuestaPublicacion extends Model
{
    protected $table = 'encuesta_publicaciones';

    protected $fillable = [
        'encuesta_denuncia_id',
        'consecutivo',
        'red_social',
        'pagina',
        'fecha_publicacion',
        'enlace',
    ];

    public function encuestaDenuncia()
    {
        return $this->belongsTo(EncuestaDenuncia::class);
    }
}
