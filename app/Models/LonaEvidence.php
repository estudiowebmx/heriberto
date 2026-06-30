<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LonaEvidence extends Model
{
    protected $table = 'lona_evidences';

    protected $fillable = [
        'lona_id',
        'consecutivo',
        'calle_y_num',
        'colonia',
        'condicion_inmueble',
        'photo_path',
    ];

    public function lona()
    {
        return $this->belongsTo(Lona::class);
    }
}
