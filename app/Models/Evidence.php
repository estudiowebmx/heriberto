<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    protected $table = 'evidences';

    protected $fillable = [
        'complaint_id',
        'address',
        'leyenda',
        'is_inhabited',
        'is_cac_president',
        'photo_path',
    ];

    protected $casts = [
        'is_inhabited' => 'boolean',
        'is_cac_president' => 'boolean',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }
}
