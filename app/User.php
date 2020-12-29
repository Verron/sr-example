<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User extends Model
{
    public $timestamps = false;

    protected $appends = ['ranking'];

    /**
     * Players only local scope
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfPlayers($query): Builder
    {
        return $query->where('user_type', 'player');
    }

    public function getIsGoalieAttribute(): bool
    {
        return (bool) $this->can_play_goalie;
    }

    public function getFullnameAttribute(): string
    {
        return Str::title($this->first_name . ' ' . $this->last_name);
    }

    public function getRankingAttribute(): int
    {
        // 3 is the default ranking per SQL file. Is there a system level setting that controls this?
        return $this->latestRanking->ranking ?? Ranking::DEFAULT_RANK;
    }

    public function setRankingAttribute($ranking)
    {
        $ranking = $this->rankings()->create(['ranking' => $ranking]);

        $this->latest_ranking_id = $ranking->id;
    }

    public function latestRanking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ranking::class);
    }

    public function rankings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasmany(Ranking::class);
    }
}
