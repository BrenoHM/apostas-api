<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'home_id',
        'away_id',
        'name_home',
        'name_away',
        'odd',
        'bet',
        'bet_value',
        'winning_tem',
        'status'
    ];

    // protected $casts = [
    //     'created_at' => 'datetime:d/m/Y H:i',
    // ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d/m/Y H:i');
    }

}
