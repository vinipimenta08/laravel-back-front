<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FinancyController extends Controller
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
            return view('financy.search', ['clients' => $clients, 'user' => $user]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
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
    public function store(Request $request)
    {

        try {
            if (!$request->search_campaign) {
                $request->request->add(['search_campaign' => "0"]);
            }

            $minDate = Carbon::now()->subYears(10);
            $minDateFormat = $minDate->format('d/m/Y');

            $maxDate = Carbon::now()->addYears(10);
            $maxDateFormat = $maxDate->format('d/m/Y');

            $validator = Validator::make($request->all(), [
                "date_init" => "after_or_equal:".$minDate->format('Y-m-d')."|before_or_equal:".$maxDate->format('Y-m-d') . "|date_format:Y-m-d",
                "date_end" => "after_or_equal:".$minDate->format('Y-m-d')."|before_or_equal:".$maxDate->format('Y-m-d') . "|date_format:Y-m-d",
            ],[
                "after_or_equal" => "O campo :attribute deve conter uma data superior ou igual a $minDateFormat.",
                "before_or_equal" => "O campo :attribute deve conter uma data inferior ou igual a $maxDateFormat.",
                "date_format" => "A data informada para o campo :attribute não está no formato correto."
            ]);
            if ($validator->fails()) {
                return response()->json(LibraryController::responseApi([], $validator->getMessageBag(), 100));
            }
            $libraryController = new LibraryController;
            $sended = $libraryController->requestAsync('GET', '/api/clients/valuesended', $request->all());

            if(isset($request->search_campaign) && $request->search_campaign == 1){

                $user = $libraryController->requestAsync("POST", "/api/me");
                return view('financy.listacostcenter',['sended' => $sended['data'], 'user' => $user]);
            }
            return view('financy.lista',['sended' => $sended['data']]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleSearchError'), "message" => __('messages.defaultMessage')], "", 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
