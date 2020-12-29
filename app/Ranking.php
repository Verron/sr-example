<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    const DEFAULT_RANK = 3;

    protected $fillable = [
        'user_id',
        'ranking',
    ];
}
