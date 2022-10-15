<?php

namespace App\Services;

use App\Mail\XMMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class XMService {

    public function __construct()
    {}

    /**
     * Fetches all company details
     */
    public function getCompanySymbols() {
        $url = 'https://pkgstore.datahub.io/core/nasdaq-listings/nasdaq-listed_json/data/a5bc7580d6176d60ac0b2142ca8d7df6/nasdaq-listed_json.json';
        $response = Http::get($url);
        if($response->ok()){
            return $response->json();
        }else{
            return null;
        }
    }

    /**
     * Get the full details of a symbol
     */
    public function getSymbolDetails($symbol){
        $symbols = $this->getCompanySymbols();
        foreach($symbols as $data){
            if($symbol == $data['Symbol']){
                return $data;
            }
        }
        return null;
    }

   
    public function sendMail($startDate, $endDate, $subject){
        Mail::to('victokala@gmail.com')->send(new XMMail($startDate, $endDate, $subject));
    }
}