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

    /**
     * Endpoint to validate request
     */
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
                $email = $request->get('email');
                $subject = $companySymbol['Company Name'];
                $this->xmService->sendMail($email, $startDate, $endDate, $subject); //send mail
                return response()->json(['success' => true, 'messages' => []]);
            }else{ //symbol doesn't exist or is invalid
                $messages['symbol'] = 'The symbol field is not valid';
                return response()->json(['success' => false, 'messages' => $messages]);
            }
        }else{
            return response()->json(['success' => false, 'messages' => $validator->messages()]);
        }

    }

    public function historicalData($symbol){
        $data = $this->xmService->getHistoricalData($symbol);
        if($data){
            return response()->json(['success' => true, 'data' => $data]);
        }else{
            return response()->json(['success' => false, 'data' => []]);
        }
    }
    
    public function getSymbolData(){
        $data = $this->xmService->getCompanySymbols();
        if($data){
            return response()->json(['success' => true, 'data' => $data]);
        }else{
            return response()->json(['success' => false, 'data' => []]);
        }
    }
}
