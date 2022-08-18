<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Request as RequestGuzzle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LibraryController extends Controller
{
    public static function requestAsync($method = 'GET', $route, $body = null, $head = null)
    {
        try {
            if (!$head) {
                $tokenController = new TokenController;
                $head = $tokenController->getheaderAuth();
            }
            $client = new Client();
            if ($body) {
                $request = new RequestGuzzle($method, env('END_POINT_BACKEND') . $route, $head, json_encode($body));
                $promise = $client->sendAsync($request)->then(function ($response) {
                    return json_decode($response->getBody()->getContents(),true);
                });
            } else {
                $request = new RequestGuzzle($method, env('END_POINT_BACKEND') . $route, $head);
                $promise = $client->sendAsync($request)->then(function ($response) {
                    return json_decode($response->getBody()->getContents(),true);
                });
            }
            $result = $promise->wait();
            if (isset($result['error']) && $result['error']) {
                throw new Exception();
            }
            return $result;
        } catch (Exception $e) {
            throw $e;
        }

    }


    public static function responseApi($data = [], $message = '', $error = 0): Array
    {
        return [
            'error' => $error,
            'data' => $data,
            'message' => $message
        ];
    }

    public function verficaDispositivo()
    {
        @$MSIE = strpos($_SERVER['HTTP_USER_AGENT'],"MSIE");
        @$Firefox = strpos($_SERVER['HTTP_USER_AGENT'],"Firefox");
        @$Mozilla = strpos($_SERVER['HTTP_USER_AGENT'],"Mozilla");
        @$Chrome = strpos($_SERVER['HTTP_USER_AGENT'],"Chrome");
        @$Chromium = strpos($_SERVER['HTTP_USER_AGENT'],"Chromium");
        @$Safari = strpos($_SERVER['HTTP_USER_AGENT'],"Safari");
        @$Opera = strpos($_SERVER['HTTP_USER_AGENT'],"Opera");

        if ($MSIE == true) { $navegador = "IE"; }
        else if ($Firefox == true) { $navegador = "Firefox"; }
        else if ($Mozilla == true) { $navegador = "Firefox"; }
        else if ($Chrome == true) { $navegador = "Chrome"; }
        else if ($Chromium == true) { $navegador = "Chromium"; }
        else if ($Safari == true) { $navegador = "Safari"; }
        else if ($Opera == true) { $navegador = "Opera"; }
        else { @$navegador = $_SERVER['HTTP_USER_AGENT']; }

        $mobile = FALSE;
        $user_agents = array("iPhone","iPad","iPod");
        $iphone = array("iPhone","iPad", "Safari");
        $valida_iphone = 'false';
        foreach($user_agents as $user_agent){
            if (strpos(@$_SERVER['HTTP_USER_AGENT'], $user_agent) !== FALSE) {
                $mobile = TRUE;
                $modelo	= $user_agent;
                if(in_array($modelo, $iphone) ){
                    $valida_iphone = 'true';
                }
                break;
            }
        }

        $link = false;
        if (!$mobile){
            if(!in_array($navegador, $iphone)){
                $link = true;
            }
        }


        return $link;
    }

    public static function is_utf8($str){
        $c=0; $b=0;
        $bits=0;
        $len=strlen($str);
        for($i=0; $i<$len; $i++){
            $c=ord($str[$i]);
            if($c > 128){
                if(($c >= 254)) return false;
                elseif($c >= 252) $bits=6;
                elseif($c >= 248) $bits=5;
                elseif($c >= 240) $bits=4;
                elseif($c >= 224) $bits=3;
                elseif($c >= 192) $bits=2;
                else return false;
                if(($i+$bits) > $len) return false;
                while($bits > 1){
                    $i++;
                    $b=ord($str[$i]);
                    if($b < 128 || $b > 191) return false;
                    $bits--;
                }
            }
        }
        return true;
    }

    public static function recordError(Exception $error)
    {
        try {
            $body = [
                'Message' => $error->getMessage(),
                'Code' => $error->getCode(),
                'File' => $error->getFile(),
                'Line' => $error->getLine(),
                'TraceAsString' => $error->getTraceAsString()
            ];
            LibraryController::requestAsync('POST', '/api/logsystem/recorderror',$body);
            return true;
        } catch (Exception $e) {
            Log::debug($e);
        }
    }

    public static function validClientJustSMS()
    {
        try {

            $clients = LibraryController::requestAsync('GET', '/api/clients');
            if(count($clients['data']) > 1){
                $just_sms = 1;
                foreach ($clients['data'] as $row) {
                    if($row['just_sms'] == 0){
                        $just_sms = $row['just_sms'];
                        break;
                    }
                }
            }else{
                $just_sms = $clients['data'][0]['just_sms'];
            }

            return $just_sms;

        } catch (Exception $e) {
            Log::debug('log: '. $e);
        }
    }
}
