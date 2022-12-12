<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Requests\StoreDepositRequest;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MercadoPago;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDepositRequest $request)
    {
        try {

            $request->merge(['user_id' => Auth::id()]);
            $response = Deposit::create($request->all());

            // if( $request->status === 'approved' ) {
            //     $user = User::find(Auth::id());
            //     $user->amount = $user->amount + $request->value;
            //     $user->save();
            // }
            
        } catch (\Exception $e) {
            $response = $e;
        }

        return Helper::responseJson($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function show(Deposit $deposit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deposit $deposit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $deposit)
    {
        //
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
}
