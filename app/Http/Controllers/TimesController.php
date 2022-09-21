<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use MercadoPago;

class TimesController extends Controller
{
    public function index()
    {
        $client = new \GuzzleHttp\Client();
        $res = $client->get('https://api.football-data-api.com/league-list?key=example');
        $data = json_decode($res->getBody()->getContents());
        return $data->data;
    }

    public function leagues()
    {
        $results = Cache::remember('leagues', 60 * 60 * 24, function() {
            $client = new \GuzzleHttp\Client();
            $res = $client->get('https://api.football-data-api.com/league-list?key=example');
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

    //somente uma liga e liberada no plano free
    public function matches()
    {
        $results = Cache::remember('matches', 60 * 60 * 24, function() {
            $client = new \GuzzleHttp\Client();
            $res = $client->get('https://api.football-data-api.com/league-matches?key=example&season_id=1625');
            $matches = json_decode($res->getBody()->getContents());
            return $matches->data;
        });
        
        return $results;
    }

    public function matchesToday()
    {
        $results = Cache::remember('matches-today', 60 * 60 * 24, function() {
            $client = new \GuzzleHttp\Client();
            $res = $client->get('https://api.football-data-api.com/todays-matches?key=example');
            $matches = json_decode($res->getBody()->getContents());
            return $matches->data;
        });
        
        return $results;
    }


    public function processPayment(Request $request)
    {
        MercadoPago\SDK::setAccessToken(env('MERCADO_PAGO_SAMPLE_ACCESS_TOKEN'));
        $post = $request->all();

        $payment = new MercadoPago\Payment();
        
        $payment->transaction_amount = (float)$post['transaction_amount'];
        $payment->token = $post['token'];
        //$payment->description = $post['description'];
        $payment->installments = (int)$post['installments'];
        $payment->payment_method_id = $post['payment_method_id'];
        $payment->issuer_id = (int)$post['issuer_id'];

        $payer = new MercadoPago\Payer();
        $payer->email = $post['payer']['email'];
        $payer->identification = array(
            "type" => $post['payer']['identification']['type'],
            "number" => $post['payer']['identification']['number']
        );
        //$payer->first_name = $post['cardholderName'];
        $payment->payer = $payer;

        $payment->save();

        if( $payment->status === 'approved' ) {
            $user = User::find(Auth::id());
            $user->amount = $user->amount + $payment->transaction_amount;
            $user->save();
        }

        $response = array(
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'id' => $payment->id
        );
        
        return $response;
    }
}
