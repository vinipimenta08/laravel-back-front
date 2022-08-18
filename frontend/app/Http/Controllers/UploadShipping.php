<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Illuminate\Http\Request;

class UploadShipping extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!$request->request_server) {
                return redirect(route('layout'));
            }
            $libraryController = new LibraryController;
            $campaigns = $libraryController->requestAsync('GET', '/api/campaigns');

            return view('uploadshipping.index', ['campaigns' => $campaigns['data']]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function downloadlayout()
    {
        $libraryController = new LibraryController;
        $validClientJustSMS = $libraryController->validClientJustSMS();
        if($validClientJustSMS){
            return view('uploadshipping.downloadlayout_justSMS');
        }else{
            return view('uploadshipping.downloadlayout');
        }

    }

    public function testevalidateupload(Request $request)
    {
        try{
            $file_path = $request->file('file')->getPathName();
            $objeto = fopen($file_path, "r");
            $lines = [];
            $custom = [];
            while (($dados = fgetcsv($objeto, 4096, ";")) != FALSE) {
                $lines[] = $dados;
            }
            foreach ($lines as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                if (!$value[0]) {
                    continue;
                }
                foreach ($value as $key2 => $value2) {
                    if(!LibraryController::is_utf8($value2)){
                        $value[$key2] = iconv("iso-8859-2", "utf-8", $value2);
                    }
                }

                $date = '';
                if (isset($value[3])) {
                    if (DateTime::createFromFormat('d/m/Y', $value[3]) !== FALSE) {
                        $date = DateTime::createFromFormat('d/m/Y', $value[3]);
                        $date = $date->format('Y-m-d');
                    }else{
                        $date = $value[3];
                    }
                }

                $custom[] = [
                    "phone"=> isset($value[0]) ? $value[0] : '' ,
                    "message_sms"=> isset($value[1]) ? $value[1] : '' ,
                    "title"=> isset($value[2]) ? $value[2] : '' ,
                    "date_event"=> $date,
                    "description"=> isset($value[4]) ? $value[4] : '' ,
                    "location"=> isset($value[5]) ? $value[5] : '' ,
                    "identification"=> isset($value[6]) ? $value[6] : '' ,
                    "joker_one"=> isset($value[7]) ? $value[7] : '' ,
                    "joker_two"=> isset($value[8]) ? $value[8] : ''
                ];
            }
            $libraryController = new LibraryController;
            $customValidate = $libraryController->requestAsync('POST', '/api/listcustom/validateListCustom', ['custom' => $custom]);
            return $libraryController->responseApi($customValidate['data']);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleValidateFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function analyzelist(Request $request)
    {
        try{
            $file_path = $request->file('file')->getPathName();
            $objeto = fopen($file_path, "r");
            $lines = [];
            $custom = [];
            while (($dados = fgetcsv($objeto, 4096, ";")) != FALSE) {
                $lines[] = $dados;
            }
            foreach ($lines as $key => $value) {
                if ($key == 0) {
                    continue;
                }
                if ($key > 4) {
                    break;
                }
                foreach ($value as $key2 => $value2) {
                    if(!LibraryController::is_utf8($value2)){
                        $value[$key2] = iconv("iso-8859-2", "utf-8", $value2);
                    }
                }
                $date = '';
                if (isset($value[3])) {
                    if (DateTime::createFromFormat('d/m/Y', $value[3]) !== FALSE) {
                        $date = DateTime::createFromFormat('d/m/Y', $value[3]);
                        $date = $date->format('Y-m-d');
                    }else{
                        $date = $value[3];
                    }
                }

                $custom[] = [
                    "phone"=> isset($value[0]) ? $value[0] : '' ,
                    "message_sms"=> isset($value[1]) ? $value[1] : '' ,
                    "title"=> isset($value[2]) ? $value[2] : '' ,
                    "date_event"=> $date,
                    "description"=> isset($value[4]) ? $value[4] : '' ,
                    "location"=> isset($value[5]) ? $value[5] : '' ,
                    "identification"=> isset($value[6]) ? $value[6] : '' ,
                    "joker_one"=> isset($value[7]) ? $value[7] : '' ,
                    "joker_two"=> isset($value[8]) ? $value[8] : ''
                ];
            }
            $libraryController = new LibraryController;
            $validClientJustSMS = $libraryController->validClientJustSMS();
            return view('uploadshipping.analyzelist', ['customs' => $custom, 'validClientJustSMS' => $validClientJustSMS]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleValidateFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function testeuploadlist(Request $request)
    {
        try{
            $file_path = $request->file('file')->getPathName();
            $objeto = fopen($file_path, "r");
            $custom = [];
            $lines_file = [];
            $response = [];
            while (($dados = fgetcsv($objeto, 4096, ";")) != FALSE) {
                $lines_file[] = $dados;
            }
            foreach (array_chunk($lines_file, 5000) as $key => $lines) {
                foreach ($lines as $key => $value) {
                    if ($key == 0) {
                        continue;
                    }
                    foreach ($value as $key2 => $value2) {
                        if(!LibraryController::is_utf8($value2)){
                            $value[$key2] = iconv("iso-8859-2", "utf-8", $value2);
                        }
                    }
                    $date = '';
                    if (isset($value[3])) {
                        if (DateTime::createFromFormat('d/m/Y', $value[3]) !== FALSE) {
                            $date = DateTime::createFromFormat('d/m/Y', $value[3]);
                            $date = $date->format('Y-m-d');
                        }else{
                            $date = $value[3];
                        }
                    }

                    $custom[] = [
                        "phone"=> isset($value[0]) ? $value[0] : '' ,
                        "message_sms"=> isset($value[1]) ? $value[1] : '' ,
                        "title"=> isset($value[2]) ? $value[2] : '' ,
                        "date_event"=> $date,
                        "description"=> isset($value[4]) ? $value[4] : '' ,
                        "location"=> isset($value[5]) ? $value[5] : '' ,
                        "identification"=> isset($value[6]) ? $value[6] : '' ,
                        "joker_one"=> isset($value[7]) ? $value[7] : '' ,
                        "joker_two"=> isset($value[8]) ? $value[8] : ''
                    ];
                }
                $body = [
                    "campaign" => $request->id_campanha,
                    "send_sms" => ($request->check_envio_sms == "true" ? '1' : 0),
                    "custom" => $custom
                ];
                unset($custom);
                $libraryController = new LibraryController;
                $response = $libraryController->requestAsync('POST', '/api/listcustom/uploadcustom', $body);
                sleep(2);
            }
            return $response;
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleEndProcessError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function uploadlist(Request $request)
    {
        try{

            $id_campaign = $request->id_campanha;
            $file_name_genion = $request->file_name_genion;
            $check_envio_sms = $request->check_envio_sms;
            $check_agendamento_sms = $request->check_agendamento_sms;
            $check_verifyWhats = $request->check_verifyWhats;
            $date_schedule = $request->date_schedule;
            $errorImport = $request->errorImport;

            if($request->hasFile('file')){

                $filename = $request->file('file')->getClientOriginalName();

                $libraryController = new LibraryController;
                $campaigns = $libraryController->requestAsync('GET', "/api/campaigns/$id_campaign");
                foreach ($campaigns['data'] as $row) {
                    $id_client = $row['id_client'];
                }

                $upload = [];
                if ($check_agendamento_sms == "true") {
                    $upload = $libraryController->requestAsync('POST', '/api/listcustom/programLoadFile', ['file_name_original' => $filename, 'file_name_genion' => $file_name_genion, 'id_campaign' => $id_campaign, 'id_client' => $id_client, 'check_envio_sms' => $check_envio_sms, 'check_agendamento_sms' => $check_agendamento_sms, 'check_verifyWhats' => $check_verifyWhats, 'date_schedule' => $date_schedule]);

                }else{
                    $upload = $libraryController->requestAsync('POST', '/api/listcustom/uploadloadfile', ['file_name_original' => $filename, 'file_name_genion' => $file_name_genion, 'id_campaign' => $id_campaign, 'id_client' => $id_client, 'check_envio_sms' => $check_envio_sms, 'check_verifyWhats' => $check_verifyWhats]);
                }

                return response()->json(LibraryController::responseApi($upload, 'ok'));

            } else {
                return LibraryController::responseApi(["title" => __('Erro arquivo'), "message" => __('Não existe na pasta')], "", 500);
            }

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleValidateFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function is_utf8($str){
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

    public function validateupload(Request $request)
    {

        try{
            $id_campaign = $request->campanha;

            $libraryController = new LibraryController;
            $campaigns = $libraryController->requestAsync('GET', "/api/campaigns/$id_campaign");
            foreach ($campaigns['data'] as $row) {
                $id_client = $row['id_client'];
            }

            // Handle File Upload
            if($request->hasFile('file')){
                // dd("teste");
                // Get filename with the extension
                $filenameWithExt = $request->file('file')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('file')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore= "genion_".$id_campaign."_".$id_client."_".time().'.'.$extension;

                // Upload File
                $path = $request->file('file')->storeAs('upload', $fileNameToStore);

                $file_name_original = $filename.'.'.$extension;
                $file_name_genion =$fileNameToStore;
                $id_campaign = $id_campaign;
                $id_client = $id_client;
                $libraryController = new LibraryController;

                $ResLoadFile = [
                        'file_name_original' => $file_name_original,
                        'file_name_genion' => $file_name_genion,
                        'id_campaign' => $id_campaign,
                        'id_client' => $id_client
                ];
                $response = $libraryController->requestAsync('POST', '/api/listcustom/loadfile', $ResLoadFile);

                return $libraryController->responseApi(['invalids' => [], 'errors_validate' => [], 'file_name_genion' => $file_name_genion]);

            } else {
                return LibraryController::responseApi(["title" => __('Erro arquivo'), "message" => __('Não existe na pasta')], "", 500);
            }


        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleValidateFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function deleteupload(Request $request)
    {
        try{
            $id_campaign = $request->campanha;
            $file_name_genion = $request->file_name_genion;

            if($request->hasFile('file')){

                $filename = $request->file('file')->getClientOriginalName();

                $libraryController = new LibraryController;
                $campaigns = $libraryController->requestAsync('GET', "/api/campaigns/$id_campaign");
                foreach ($campaigns['data'] as $row) {
                    $id_client = $row['id_client'];
                }

                $responseDelete = $libraryController->requestAsync('POST', '/api/listcustom/deleteloadfile', ['file_name_original' => $filename, 'file_name_genion' => $file_name_genion, 'id_campaign' => $id_campaign, 'id_client' => $id_client]);

                return response()->json(LibraryController::responseApi($responseDelete['data'], 'ok'));

            } else {
                return LibraryController::responseApi(["title" => __('Erro arquivo'), "message" => __('Não existe na pasta')], "", 500);
            }

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleValidateFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }
}
