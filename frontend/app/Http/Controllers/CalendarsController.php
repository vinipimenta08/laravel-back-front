<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\src\Link;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CalendarsController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $hash)
    {
        try {
            $libraryController = new LibraryController;
            $device_type = "desktop";
            if($libraryController->verficaDispositivo()){
                $device_type = "mobile";
            }

            $head = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            $body = [
                'device_type' => $device_type
            ];
            $route = "/api/".$hash;
            $calendar = $libraryController->requestAsync("POST", $route , $body, $head);

            if($calendar['error'] != 0){
                return $calendar['message'];
            }
            $arrayListCustom = $calendar['data'];
            $dataEvento = date('Y-m-d', strtotime($arrayListCustom['date_event']));
            $start = date('Y-m-d 09:00:00',  strtotime($dataEvento));
            $end = date('Y-m-d 18:00:00',  strtotime($dataEvento));
            $title = $arrayListCustom['title'];
            $descricao = $arrayListCustom['description'];
            $localizacao = $arrayListCustom['location'];
            $from = DateTime::createFromFormat('Y-m-d H:i:s', $start); //criando a data de início do evento
            $to = DateTime::createFromFormat('Y-m-d H:i:s', $end); //criando a data de fim do evento
            if (!$localizacao) {
                $localizacao = '';
            }
            $link = Link::create($title, $from, $to) //definindo o título do evento
                        ->description($descricao) //definindo a descrição do evento
                        ->address($localizacao); //definindo o local do evento
            if($libraryController->verficaDispositivo()){
                /**
                 * ENVIO PELO GOOGLE
                 */
                $retorno_link = $link->google();
                return Redirect::to($retorno_link);
            }else{
                /**
                * ENVIO PELO ics
                */
                $retorno_link = $link->ics();
                $retorno_link = file_get_contents($retorno_link);
                $name = date('YmdHis');
                return response($retorno_link, 200)
                                        ->header("Content-type","text/calendar")
                                        ->header("Content-Disposition","inline; filename=event_$name.ics")
                                        ->header("Content-Length:", strlen($retorno_link))
                                        ->header("Connection","close");
            }
        } catch (\Throwable $th) {
            return "Ops! Este link expirou";
        }
    }
}
