<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MatchController extends Controller
{
    public function index()
    {
        $results = Cache::remember('matches', 60 * 60 * 24, function() {
            $client = new \GuzzleHttp\Client();
            $res = $client->get(env('BASE_URL_API').'/league-matches?key='.env('API_KEY').'&season_id=1625');
            $matches = json_decode($res->getBody()->getContents());
            return $matches->data;
        });
        
        return $results;
    }

    public function matchesToday()
    {
        $results = Cache::remember('matches-today', 60 * 60 * 24, function() {
            $client = new \GuzzleHttp\Client();
            $res = $client->get(env('BASE_URL_API').'/todays-matches?key='.env('API_KEY'));
            $matches = json_decode($res->getBody()->getContents());
            return $matches->data;
        });
        
        return $results;
    }
}
