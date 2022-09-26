<?php


namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Cache;

class Helper
{
    public static function responseJson($data, $statusCode = 200)
    {
        if ($data instanceof Validator) {
            return response()->json(['success' => false, 'errors' => $data->errors()->all(), 'data' => null], 422);
        }

        if ($data instanceof \Exception) {
            return response()->json(['success' => false, 'errors' => [$data->getMessage()], 'data' => null], 400);
        }

        return response()->json(['success' => true, 'errors' => null, 'data' => $data], $statusCode);
    }

    public static function upload($base_64)
    {
        $image = str_replace('data:image/png;base64,', '', $base_64);
        $image = str_replace(' ', '+', $image);
        $imageName = hash('md5', Carbon::now()).'.png';
        Storage::put('public/'.$imageName, base64_decode($image));

        return $imageName;
    }

    public static function isBase64($data)
    {
      return strpos($data, 'data:image') !== false;
    }

    public static function matchDetalhe($id)
    {
        $results = Cache::remember('match-' . $id, 60 * 60 * 24, function() use($id) {
            $client = new \GuzzleHttp\Client();
            $res = $client->get('https://api.football-data-api.com/match?key=example&match_id='.$id);
            $matches = json_decode($res->getBody()->getContents());
            return $matches->data;
        });
        
        return $results;
    }

}
