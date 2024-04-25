<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct(){
        $this->apiKey = config('currency_freaks.api_key');
        $this->baseUrl = 'https://api.currencyfreaks.com/v2.0/';
    }

    public function getRates(){
        $response = Http::get($this->baseUrl . 'rates/latest', [
            'apikey' => $this->apiKey,
        ]);

        $rates = $response->json()["rates"];

        return $rates;
    }


    public function getRate($cur){
        $response = Http::get($this->baseUrl . 'rates/latest', [
            'apikey' => $this->apiKey,
        ]);

        $rates = $response->json()["rates"];

        return $rates[$cur];
    }

    public function getAmountConversted($cur, $amount){
        $response = Http::get($this->baseUrl . 'rates/latest', [
            'apikey' => $this->apiKey,
        ]);

        $rates = $response->json()["rates"];

        return $rates[$cur] * $amount;
    }
}
