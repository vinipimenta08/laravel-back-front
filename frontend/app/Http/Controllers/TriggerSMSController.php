<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TriggerSMSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if (!$request->request_server) {
                return redirect(route('layout'));
            }
            $libraryController = new LibraryController;
            $clients = $libraryController->requestAsync("GET", "/api/clients");
            $user = $libraryController->requestAsync("POST", "/api/me");
            return view('trigger_sms.search', ['clients' => $clients, 'user' => $user]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            $minDate = Carbon::now()->subYears(10);
            $minDateFormat = $minDate->format('d/m/Y');

            $maxDate = Carbon::now()->addYears(10);
            $maxDateFormat = $maxDate->format('d/m/Y');

            $validator = Validator::make($request->all(), [
                "init_date" => "after_or_equal:".$minDate->format('Y-m-d')."|before_or_equal:".$maxDate->format('Y-m-d'). "|date_format:Y-m-d",
                "end_date" => "after_or_equal:".$minDate->format('Y-m-d')."|before_or_equal:".$maxDate->format('Y-m-d'). "|date_format:Y-m-d",
            ],[
                "after_or_equal" => "O campo :attribute deve conter uma data superior ou igual a $minDateFormat.",
                "before_or_equal" => "O campo :attribute deve conter uma data inferior ou igual a $maxDateFormat.",
                "date_format" => "A data informada para o campo :attribute não está no formato correto."
            ]);
            if ($validator->fails()) {
                return response()->json(LibraryController::responseApi([], $validator->getMessageBag(), 100));
            }
            $libraryController = new LibraryController;
            $campaigns = $libraryController->requestAsync('GET', '/api/campaigns/statuscampaigns', $request->all());
            $statuslink = $libraryController->requestAsync("GET", "/api/statuslink");
            return view('trigger_sms.lista',['campaigns' => $campaigns['data'], 'statuslink' => $statuslink]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleSearchError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    
    }


    /**
     * Resend sms
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resendSMS(Request $request)
    {
        try {
            $libraryController = new LibraryController;
            $resendSMS = $libraryController->requestAsync('POST', '/api/sms/resend', $request->all());
            return $resendSMS;
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleResendError'), "message" => __('messages.defaultMessage')], "", 500);
        }

    }

}
