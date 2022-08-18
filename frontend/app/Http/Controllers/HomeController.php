<?php

namespace App\Http\Controllers;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function layout()
    {
        $libraryController = new LibraryController;
        $menus = $libraryController->requestAsync("GET", "/api/menus");
        $firstMenus = [
            'id' => '',
            'href' => 'test'
        ];
        foreach ($menus['data'] as $key => $value) {
            if ($value['slug'] == 'link') {
                $firstMenus = $value;
                break;
            }
        }
        return view('layouts.app', ["firstMenus" => $firstMenus]);
    }
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
            return view('home');
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function dashData(Request $request)
    {
        try {
            $card = [];
            $listCustom = [];
            $user_acount = [];
            $lastDays = [];
            $states = [];
            try {
                $libraryController = new LibraryController;
                $dashdata = $libraryController->requestAsync("GET", "/api/home/dashdata", $request->all());
                
                $card = $dashdata['data']['card'];
                $listCustom = $dashdata['data']['listCustom'];
                $user_acount = $dashdata['data']['userAcount'];
                $lastDays = $dashdata['data']['lastDays'];
                $states = $dashdata['data']['greateropening'];
            } catch (Exception $e) {
                LibraryController::recordError($e);
                return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
            }
            $sendedTotal = 0;
            $openingTotal = 0;
            $campaignsTotal = 0;
            $importedTotal = 0;
            $replyTotal = 0;
            foreach ($card['data'] as $key => $value) {
                $openingTotal += $value['opening'];
                $replyTotal += $value['reply'];
            }
            foreach ($listCustom['data'] as $key => $value) {
                $importedTotal += $value['imported'];
                $sendedTotal += $value['sended'];
                if (isset($value['campaign']['name']) != "") {
                    $campaignsTotal += 1;
                }                
            }

            $cardTotal = [
                'sended' => $sendedTotal,
                'opening' => $openingTotal,
                'campaigns' => $campaignsTotal,
                'imported' => $importedTotal,
                'reply' => $replyTotal
            ];


            // GRAFICO RESPOSTA
            $inicio = new DateTime( date("Y-m-d",strtotime(date("Y-m-d")."-6 day")) );
            $fim    = new DateTime( date("Y-m-d",strtotime(date("Y-m-d")."+1 day")) );

            $frequencia = new DateInterval('P1D');
            $intervalo = new DatePeriod($inicio, $frequencia ,$fim);

            $count_day=0;
            $response_day_1 = 0;
            $dayofweek_1 = "";
            $response_day_2 = 0;
            $dayofweek_2 = "";
            $response_day_3 = 0;
            $dayofweek_3 = "";
            $response_day_4 = 0;
            $dayofweek_4 = "";
            $response_day_5 = 0;
            $dayofweek_5 = "";
            $response_day_6 = 0;
            $dayofweek_6 = "";
            $response_day_7 = 0;
            $dayofweek_7 = "";
            foreach ($intervalo as $data){
                $date = $data->format("Y-m-d");
                $dateofday = $data->format("Y-m-d");
                $count_day++;

                if ($lastDays['data']) {

                    $voltar = 0;
                    foreach ($lastDays['data'] as $days) {    
                        
                        if ($voltar == 0) {                        

                            if ($days['date_received'] == $date) {

                                $countResposta = $days['total'];
                                $voltar = 1;
                            }else{

                                $countResposta = 0;

                            }

                        }

                    }

                    $dayofweek = date('l', strtotime($dateofday));
                    $day = substr($dayofweek, 0, 3);

                    if ($day == "Sun") {
                        $day = "Dom";
                    }elseif ($day == "Mon") {
                        $day = "Seg";
                    }elseif ($day == "Tue") {
                        $day = "Ter";
                    }elseif ($day == "Wed") {
                        $day = "Qua";
                    }elseif ($day == "Thu") {
                        $day = "Qui";
                    }elseif ($day == "Fri") {
                        $day = "Sex";
                    }else{
                        $day = "Sáb";
                    }

                    switch ($count_day) {
                        case 1:
                            $response_day_1 = $countResposta;
                            $dayofweek_1 = $day;
                        case 2:
                            $response_day_2 = $countResposta;
                            $dayofweek_2 = $day;
                        case 3:
                            $response_day_3 = $countResposta;
                            $dayofweek_3 = $day;
                        case 4:
                            $response_day_4 = $countResposta;
                            $dayofweek_4 = $day;
                        case 5:
                            $response_day_5 = $countResposta;
                            $dayofweek_5 = $day;
                        case 6:
                            $response_day_6 = $countResposta;
                            $dayofweek_6 = $day;
                        case 7:
                            $response_day_7 = $countResposta;
                            $dayofweek_7 = $day;
                    }

                }else{

                    $dayofweek = date('l', strtotime($dateofday));
                    $day = substr($dayofweek, 0, 3);

                    if ($day == "Sun") {
                        $day = "Dom";
                    }elseif ($day == "Mon") {
                        $day = "Seg";
                    }elseif ($day == "Tue") {
                        $day = "Ter";
                    }elseif ($day == "Wed") {
                        $day = "Qua";
                    }elseif ($day == "Thu") {
                        $day = "Qui";
                    }elseif ($day == "Fri") {
                        $day = "Sex";
                    }else{
                        $day = "Sáb";
                    }

                    switch ($count_day) {
                        case 1:
                            $response_day_1 = 0;
                            $dayofweek_1 = $day;
                        case 2:
                            $response_day_2 = 0;
                            $dayofweek_2 = $day;
                        case 3:
                            $response_day_3 = 0;
                            $dayofweek_3 = $day;
                        case 4:
                            $response_day_4 = 0;
                            $dayofweek_4 = $day;
                        case 5:
                            $response_day_5 = 0;
                            $dayofweek_5 = $day;
                        case 6:
                            $response_day_6 = 0;
                            $dayofweek_6 = $day;
                        case 7:
                            $response_day_7 = 0;
                            $dayofweek_7 = $day;
                    }

                }

            }

            $last_days = [
                'response_day_1' => $response_day_1,
                'dayofweek_1' => $dayofweek_1,
                'response_day_2' => $response_day_2,
                'dayofweek_2' => $dayofweek_2,
                'response_day_3' => $response_day_3,
                'dayofweek_3' => $dayofweek_3,
                'response_day_4' => $response_day_4,
                'dayofweek_4' => $dayofweek_4,
                'response_day_5' => $response_day_5,
                'dayofweek_5' => $dayofweek_5,
                'response_day_6' => $response_day_6,
                'dayofweek_6' => $dayofweek_6,
                'response_day_7' => $response_day_7,
                'dayofweek_7' => $dayofweek_7
            ];

            return view('dashboard.index', ['card' => $card, 'cardTotal' => $cardTotal, 'listCustom' => $listCustom, 'user_acount' => $user_acount, 'last_days' => $last_days, 'states' => $states]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function sessionexpire()
    {
        return response('sessão expirada')->status(100);
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
        //
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

    public static function startapplication()
    {
        try {
            $libraryController = new LibraryController;
            $response = $libraryController->requestAsync('GET', '/api/menus/aplication');
            return $response;
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }
}
