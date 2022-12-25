<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rates extends Model
{
    protected $fillable = [
        'name',
        'data',
        'date',
    ];

    protected $protected = [
        'id'
    ];

    static public function api_data(Carbon $date)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://currate.ru/api/?get=rates&pairs=BTCUSD&key=' . env('API_KEY') . '&date=' . $date->toDateString());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        return self::create([
            'name' => 'BTCUSD',
            'data' => $data->data->BTCUSD,
            'date' => $date,
        ]);
    }
}
