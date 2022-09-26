<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeagueController extends Controller
{
    public function index()
    {
        $results = Cache::remember('leagues', 60 * 60 * 24, function() {
            $client = new \GuzzleHttp\Client();
            $res = $client->get(env('BASE_URL_API').'/league-list?key='.env('API_KEY'));
            $data = [];
            $leagues = json_decode($res->getBody()->getContents());
            foreach( $leagues->data as $league  ) {
                if( $league->country === 'Brazil' ) {
                    array_push($data, $league);
                }
            }
            return $data;
        });
        
        return $results;
    }
}
