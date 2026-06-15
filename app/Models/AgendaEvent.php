<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaEvent extends Model
{
    protected $table = 'agenda_events';

    protected $fillable = [
        'date',
        'title',
        'description',
        'location',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
