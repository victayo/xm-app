<?php

namespace App\Http\Controllers;

use App\Mail\XMMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\XMService;

class XMController extends Controller
{
    private $xmService;

    public function __construct(XMService $xmService)
    {
        $this->xmService = $xmService;
    }

    public function historicalData(Request $request){

    }

    public function submit(Request $request){
        $rules = [
            'email' => 'required|email:rfc,dns',
            'startDate' => 'required|date|before_or_equal:endDate|before_or_equal:today',
            'endDate' => 'required|date|after_or_equal:startDate',
            'symbol' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);

        if(!$validator->fails()){ //request is valid
            $symbol = $request->get('symbol');
            $companySymbol = $this->xmService->getSymbolDetails($symbol);
            if(!is_null($companySymbol)){ //symbol exists
                $startDate = $request->get('startDate');
                $endDate = $request->get('endDate');
                $this->xmService->sendMail($startDate, $endDate, $companySymbol['Company Name']); //send mail
                return response()->json(['success' => true, 'messages' => []]);
            }else{ //symbol doesn't exist or is invalid
                $messages['symbol'] = 'The symbol field is not valid';
                return response()->json(['success' => false, 'messages' => $messages]);
            }
        }else{
            return response()->json(['success' => false, 'messages' => $validator->messages()]);
        }

    }

    private function isValid($symbol){
        if(!$symbol){
            return false;
        }
        $response = $this->xmService->getCompanySymbols();
        foreach($response as $data){
            if($symbol == $data['Symbol']){
                return true;
            }
        }
        return false;
    }
    
}
