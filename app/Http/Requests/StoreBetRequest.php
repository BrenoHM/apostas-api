<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreBetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function validationData() {
        return array_merge(
            $this->all(),
            [
                'user_id' => Auth::id()
            ]
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => [
                'required',
                Rule::unique('bets')->where('match_id', $this->match_id)->where('bet', $this->bet)
            ],
            "match_id" => 'required',
            "home_id" => 'required',
            "away_id" => 'required',
            'name_home' => 'required',
            'name_away' => 'required',
            "odd" => 'required',
            "bet" => 'required',
            "bet_value" => 'required',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'Usuário não informado.',
            'user_id.unique' => 'Aposta já realizada.',
        ];
    }

    protected function failedValidation(Validator $validator) : void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors()->all(),
            'data' => null]
        , 422));
    }
}
