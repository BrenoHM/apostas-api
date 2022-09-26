<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Requests\StoreBetRequest;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BetController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = Bet::where('user_id', Auth::id())
                        ->where('status', $request->filter ?? 'opened')
                        ->get();
                        
        return Helper::responseJson($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBetRequest $request)
    {
        try {

            $user = User::find(Auth::id());

            if( $user->amount < (float)$request->bet_value ) {
                throw new \Exception('Saldo indisponível para esta aposta');
            }

            $request->merge(['user_id' => Auth::id()]);
            $response = Bet::create($request->all());
            $user->amount = $user->amount - (float)$request->bet_value;
            $user->save();
            
        } catch (\Exception $e) {
            $response = $e;
        }

        return Helper::responseJson($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bet  $bet
     * @return \Illuminate\Http\Response
     */
    public function show(Bet $bet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bet  $bet
     * @return \Illuminate\Http\Response
     */
    public function edit(Bet $bet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bet  $bet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bet $bet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bet  $bet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bet $bet)
    {
        //
    }
}
