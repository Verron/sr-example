<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    const DEFAULT_RANK = 3;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'ranking',
    ];
}
