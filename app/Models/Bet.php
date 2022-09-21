<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'home_id',
        'away_id',
        'odd',
        'bet',
        'bet_value',
        'winning_tem',
        'status'
    ];

}
