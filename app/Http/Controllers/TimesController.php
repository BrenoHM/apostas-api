<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Bet;
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
        $res = $client->get(env('BASE_URL_API').'/league-list?key='.env('API_KEY'));
        $data = json_decode($res->getBody()->getContents());
        return $data->data;
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

    public function processPaymentPix(Request $request)
    {
        MercadoPago\SDK::setAccessToken(env('MERCADO_PAGO_SAMPLE_ACCESS_TOKEN'));

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = $request->transactionAmount;
        $payment->description = $request->description;
        $payment->payment_method_id = "pix";
        $payment->payer = array(
            "email" => $request->email,
            "first_name" => $request->payerFirstName,
            "last_name" => $request->payerLastName,
            "identification" => array(
                "type" => $request->identificationType,
                "number" => $request->identificationNumber
            ),
            // "address"=>  array(
            //     "zip_code" => "06233200",
            //     "street_name" => "Av. das Nações Unidas",
            //     "street_number" => "3003",
            //     "neighborhood" => "Bonfim",
            //     "city" => "Osasco",
            //     "federal_unit" => "SP"
            // )
        );

        $payment->save();

        $response = array(
            'id' => $payment->id,
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'transaction_details' => $payment->transaction_details,
            'point_of_interaction' => $payment->point_of_interaction
        );

        return $response;
    }

    public function processPaymentBill(Request $request)
    {
        MercadoPago\SDK::setAccessToken(env('MERCADO_PAGO_SAMPLE_ACCESS_TOKEN'));

        $payment = new MercadoPago\Payment();
        $payment->transaction_amount = $request->transactionAmount;
        $payment->description = $request->description;
        $payment->payment_method_id = "bolbradesco";
        $payment->payer = array(
            "email" => $request->email,
            "first_name" => $request->payerFirstName,
            "last_name" => $request->payerLastName,
            "identification" => array(
                "type" => $request->identificationType,
                "number" => $request->identificationNumber
            ),
            // "address"=>  array(
            //     "zip_code" => "06233200",
            //     "street_name" => "Av. das Nações Unidas",
            //     "street_number" => "3003",
            //     "neighborhood" => "Bonfim",
            //     "city" => "Osasco",
            //     "federal_unit" => "SP"
            // )
        );

        $payment->save();

        $response = array(
            'id' => $payment->id,
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'transaction_details' => $payment->transaction_details,
        );

        return $response;
    }

    //teste de processamento de bets
    public function processBets()
    {
        //etapas do processo
        // 1 - buscar na tabela bets as apostas que estao com status opened
        // 2 - consultar a partida e verificar se o status dela esta completed
        // 3 - verificar se o id do winning team é igual o da winning id team da tabela de bets
        // 4 - se for igual, pega o valor apostado * pelo odd do time, atualiza a tabela de users com o amount
        // 5 - atualiza o stauts da tabela bets com processed e o winning team
        $bets = Bet::where('status', 'opened')->get();
        if( $bets->count() ) {
            foreach($bets as $bet) {
                //consulta partida
                $match = Helper::matchDetalhe($bet->match_id);
                if( $match ) {
                    if( $match->status == "complete" ) {
                        if( $match->winningTeam === $bet->bet ){
                            $amount = $bet->bet_value * $bet->odd;
                            $user = User::find($bet->user_id);
                            if( $user ) {
                                $user->amount = $user->amount + $amount;
                                $user->save();
                            }
                        }

                        Bet::where('id', $bet->id)->update([
                            'winning_tem' => $match->winningTeam,
                            'status' => 'processed'
                        ]);
                    }
                }
            }
        }
        return ['processed ok'];
    }
}
