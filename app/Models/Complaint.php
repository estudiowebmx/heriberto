<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'complainant_id',
        'colonia',
        'cac_president_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complainant()
    {
        return $this->belongsTo(Complainant::class);
    }

    public function evidences()
    {
        return $this->hasMany(Evidence::class);
    }

    public function getFormattedLeyendasTextAttribute(): string
    {
        $leyendas = $this->evidences->pluck('leyenda')->unique()->values();

        if ($leyendas->count() === 0) {
            return '';
        }

        if ($leyendas->count() === 1) {
            return "'{$leyendas->first()}'";
        }

        $quoted = $leyendas->map(fn($l) => "'{$l}'")->values();

        if ($leyendas->count() === 2) {
            return $quoted[0] . ' y ' . $quoted[1];
        }

        $last = $quoted->pop();

        return $quoted->implode(', ') . ' y ' . $last;
    }
}
