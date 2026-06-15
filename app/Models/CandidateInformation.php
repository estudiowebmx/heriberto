<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateInformation extends Model
{
    protected $table = 'candidate_information';

    protected $fillable = [
        'section',
        'title',
        'content',
        'order',
    ];
}
