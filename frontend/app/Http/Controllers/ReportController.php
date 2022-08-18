<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!$request->request_server) {
                return redirect(route('layout'));
            }
            $library = new LibraryController;
            $campaings = $library->requestAsync('GET', '/api/campaigns');
            return view('reports.index', ['campaings' => $campaings['data']]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function search(Request $request)
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
            $library = new LibraryController;
            $reports = $library->requestAsync("GET", "/api/campaigns/report", $request->all());
            $count=0;
            $resultReports = array();
            foreach ($reports['data'] as $key => $value) {
                foreach ($value as $key => $row) {
                    $resultReports[$count]['id'] = $row['id'];
                    $resultReports[$count]['name'] = $row['name'];
                    $resultReports[$count]['created_at'] = $row['created_at'];
                    $count++;
                }
            }
            return view('reports.search', ['reports' => $resultReports]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleSearchError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function list(Request $request)
    {
        try {
            $library = new LibraryController;
            $returnReportList = $library->requestAsync("GET", "/api/campaigns/reportlist", $request->all());
            $Currentuser = $library->requestAsync('POST', '/api/me');
            $campaign = $library->requestAsync("GET", "/api/campaigns/$request->id_campaign");

            foreach ($campaign['data'] as $row) {
                $id_client = $row['id_client'];
            }

            $validClientJustSMS = $library->validClientJustSMS($id_client);

            if(!$validClientJustSMS){ // ZENVIA AND KOLMEYA

                $count=0;
                $reportlist = array();
                foreach ($returnReportList['data'] as $key => $row) {
                    if ($request->id_campaign) {
                        $reportlist['data'][$count]['celular'] = $row['celular'];
                        $reportlist['data'][$count]['whatsApp'] = $row['whatsApp'];
                        $reportlist['data'][$count]['message_sms'] = $row['message_sms'];
                        $reportlist['data'][$count]['date_event'] = $row['date_event'];
                        $reportlist['data'][$count]['title'] = $row['title'];
                        $reportlist['data'][$count]['description'] = $row['description'];
                        $reportlist['data'][$count]['location'] = $row['location'];
                        $reportlist['data'][$count]['identification'] = $row['identification'];
                        $reportlist['data'][$count]['joker_one'] = $row['joker_one'];
                        $reportlist['data'][$count]['joker_two'] = $row['joker_two'];
                        $reportlist['data'][$count]['status'] = $row['status'];
                        $reportlist['data'][$count]['status_link'] = $row['status_link'];
                        $reportlist['data'][$count]['hash'] = $row['hash'];
                        $reportlist['data'][$count]['input_date'] = $row['input_date'];
                        $reportlist['data'][$count]['date_opened'] = $row['date_opened'];
                        $reportlist['data'][$count]['device_type'] = $row['device_type'];
                    } else {
                        $reportlist['data'][$count]['name_campaign'] = $row['name_campaign'];
                        $reportlist['data'][$count]['celular'] = $row['celular'];
                        $reportlist['data'][$count]['whatsApp'] = $row['whatsApp'];
                        $reportlist['data'][$count]['message_sms'] = $row['message_sms'];
                        $reportlist['data'][$count]['date_event'] = $row['date_event'];
                        $reportlist['data'][$count]['title'] = $row['title'];
                        $reportlist['data'][$count]['description'] = $row['description'];
                        $reportlist['data'][$count]['location'] = $row['location'];
                        $reportlist['data'][$count]['identification'] = $row['identification'];
                        $reportlist['data'][$count]['joker_one'] = $row['joker_one'];
                        $reportlist['data'][$count]['joker_two'] = $row['joker_two'];
                        $reportlist['data'][$count]['status'] = $row['status'];
                        $reportlist['data'][$count]['status_link'] = $row['status_link'];
                        $reportlist['data'][$count]['hash'] = $row['hash'];
                        $reportlist['data'][$count]['input_date'] = $row['input_date'];
                        $reportlist['data'][$count]['date_opened'] = $row['date_opened'];
                        $reportlist['data'][$count]['device_type'] = $row['device_type'];
                    }

                    $count++;
                }

                if($Currentuser['id_client'] == '16'){
                    if ($request->id_campaign) {
                        $arrayLines[] = '"telefone";"whatsApp";"mensagem_sms";"data_evento";"titulo";"descricao";"localizacao";"identificador";"coringa_1";"coringa_2";"status_sms";"status_link";"link";"data_entrada";"data_abertura";"tipo_dispositivo";"recebimento";"visualizado"';
                    } else {
                        $arrayLines[] = '"campanha";"telefone";"whatsApp";"mensagem_sms";"data_evento";"titulo";"descricao";"localizacao";"identificador";"coringa_1";"coringa_2";"status_sms";"status_link";"link";"data_entrada";"data_abertura";"tipo_dispositivo";"recebimento";"visualizado"';
                    }
                }else{
                    if ($request->id_campaign) {
                        $arrayLines[] = '"telefone";"whatsApp";"mensagem_sms";"data_evento";"titulo";"descricao";"localizacao";"identificador";"coringa_1";"coringa_2";"status_sms";"status_link";"link";"data_entrada";"data_abertura";"tipo_dispositivo"';
                    } else {
                        $arrayLines[] = '"campanha";"telefone";"whatsApp";"mensagem_sms";"data_evento";"titulo";"descricao";"localizacao";"identificador";"coringa_1";"coringa_2";"status_sms";"status_link";"link";"data_entrada";"data_abertura";"tipo_dispositivo"';
                    }
                }

                foreach ($reportlist['data'] as $key => $value) {
                    $value['hash'] = URL::to('/')."/".$value['hash'];
                    $value['input_date'] = Carbon::createFromDate($value['input_date'])->format('d/m/Y H:i:s');
                    if ($value['date_opened']) {
                        $value['date_opened'] = Carbon::createFromDate($value['date_opened'])->format('d/m/Y H:i:s');
                    }
                    if ($value['device_type']) {
                        if ($value['device_type'] == 'mobile') {
                            $value['device_type'] = 'celular';
                        }else {
                            $value['device_type'] = 'computador';
                        }
                    }
                    if ($value['whatsApp'] == 1) {
                        $value['whatsApp'] = 'POSSUI';
                    }else{
                        $value['whatsApp'] = 'NÃO POSSUI';
                    }
                    foreach ($value as $key2 => $value2) {
                        $value[$key2] = "\"" . preg_replace('/\s/',' ',$value2) . "\"";
                    }
                    if($Currentuser['id_client'] == '16'){
                        $value['recebimento'] = ($value['status'] == '"SMS ENVIADO"')? $value['input_date'] : '';
                        $value['visualizado'] = ($value['status_link'] == '"VISUALIZADO"')? 1 : 0;
                    }
                    $arrayLines[] = implode(';', $value);
                }

            }else{ // TALKIP

                $count=0;
                $reportlist = array();
                foreach ($returnReportList['data'] as $key => $row) {
                    if ($request->id_campaign) {
                        $reportlist['data'][$count]['celular'] = $row['celular'];
                        $reportlist['data'][$count]['message_sms'] = $row['message_sms'];
                        $reportlist['data'][$count]['status'] = $row['status'];
                        $reportlist['data'][$count]['input_date'] = $row['input_date'];
                    } else {
                        $reportlist['data'][$count]['name_campaign'] = $row['name_campaign'];
                        $reportlist['data'][$count]['celular'] = $row['celular'];
                        $reportlist['data'][$count]['message_sms'] = $row['message_sms'];
                        $reportlist['data'][$count]['status'] = $row['status'];
                        $reportlist['data'][$count]['input_date'] = $row['input_date'];
                    }

                    $count++;
                }

                if($Currentuser['id_client'] == '16'){
                    if ($request->id_campaign) {
                        $arrayLines[] = '"telefone";"mensagem_sms";"data_evento";"titulo";"descricao";"localizacao";"identificador";"coringa_1";"coringa_2";"status_sms";"status_link";"link";"data_entrada";"data_abertura";"tipo_dispositivo";"recebimento";"visualizado"';
                    } else {
                        $arrayLines[] = '"campanha";"telefone";"mensagem_sms";"data_evento";"titulo";"descricao";"localizacao";"identificador";"coringa_1";"coringa_2";"status_sms";"status_link";"link";"data_entrada";"data_abertura";"tipo_dispositivo";"recebimento";"visualizado"';
                    }
                }else{
                    if ($request->id_campaign) {
                        $arrayLines[] = '"telefone";"mensagem_sms";"status_sms";"data_entrada"';
                    } else {
                        $arrayLines[] = '"campanha";"telefone";"mensagem_sms";"status_sms";"data_entrada"';
                    }
                }

                foreach ($reportlist['data'] as $key => $value) {
                    $value['input_date'] = Carbon::createFromDate($value['input_date'])->format('d/m/Y H:i:s');

                    foreach ($value as $key2 => $value2) {
                        $value[$key2] = "\"" . preg_replace('/\s/',' ',$value2) . "\"";
                    }
                    if($Currentuser['id_client'] == '16'){
                        $value['recebimento'] = ($value['status'] == '"SMS ENVIADO"')? $value['input_date'] : '';
                    }
                    $arrayLines[] = implode(';', $value);
                }

            }

            if (count($arrayLines) > 0) {

                $csv = implode("\n", $arrayLines);
                $name = 'lista';
                if ($request->id_campaign) {
                    $name = $name . '_' . $request->id_campaign;
                    $name = $name . '_' . Carbon::createFromDate($request->init_date)->format('Ymd');
                }else {
                    $name = $name . '_' . Carbon::createFromDate($request->init_date)->format('Ymd');
                    $name = $name . '_' . Carbon::createFromDate($request->end_date)->format('Ymd');
                }
                return ['content' => $csv, 'name' => $name];
            }

            return ['erro' => 1, 'title' => "Vazio!", 'message' => "Nenhum dado encontrado."];

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDownloadFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function errors(Request $request)
    {

        try {
            $library = new LibraryController;
            $reporterrors = $library->requestAsync("GET", "/api/campaigns/reporterrors", $request->all());
            $sortLogError = [];
            foreach ($reporterrors['data'] as $key => $value) {
                $sortLogError[$value['name_campaign']][$value['name_file']][] = $value;
            }
            $errors = '';

            if (count($reporterrors['data']) > 0) {
                foreach ($sortLogError as $key => $value) {
                    $count_line = 1;
                    $date_now = Carbon::now()->format('Y-m-d H:i:s');
                    if (!$errors) {
                        $errors .= "$date_now: Iniciando arquivo...";
                    }else {
                        $errors .= "\n$date_now: Iniciando arquivo...";
                    }
                    $errors .= "\n$date_now: Centro de custo: $key";
                    foreach ($value as $key2 => $value2) {
                        $date_now = Carbon::now()->format('Y-m-d H:i:s');
                        $errors .= "\n$date_now: Lista -> $key2";
                        foreach ($value2 as $key3 => $value3) {
                            $date_now = Carbon::now()->format('Y-m-d H:i:s');
                            $errors .= "\n$date_now: Linha -> $count_line";
                            $errors .= "\n$date_now: Linha do arquivo com erro -> {$value3['line_file']}";
                            $errors .= "\n$date_now: Quantidade de erros na linha -> {$value3['qtd_errors']}";
                            $errors .= "\n$date_now: Telefone do Registro ({$value3['phone']})";
                            $errors .= "\n$date_now: Campos com erros -> {$value3['fields_errors']}";
                            $count_line++;
                        }
                    }
                }
                $name = 'log';
                if ($request->id_campaign) {
                    $name = $name . '_list_' . $request->id_campaign;
                    $name = $name . '_' . Carbon::createFromDate($request->init_date)->format('Ymd');
                }else {
                    $name = $name . '_' . Carbon::createFromDate($request->init_date)->format('Ymd');
                    $name = $name . '_' . Carbon::createFromDate($request->end_date)->format('Ymd');
                }
                return ['content' => $errors, 'name' => $name];
            }

            return ['erro' => 1, 'title' => "Vazio!", 'message' => "Nenhum dado encontrado."];

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDownloadFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function reply(Request $request)
    {

        try {
            $library = new LibraryController;
            $returnReportreply = $library->requestAsync("GET", "/api/campaigns/reportreply", $request->all());
            $campaign = $library->requestAsync("GET", "/api/campaigns/$request->id_campaign");

            foreach ($campaign['data'] as $row) {
                $id_client = $row['id_client'];
            }

            $validClientJustSMS = $library->validClientJustSMS($id_client);

            $reportreply = array();
            if(!$validClientJustSMS){ // ZENVIA AND KOLMEYA

                if ($request->id_campaign) {
                    $arrayLines[] = '"resposta";"telefone";"data_evento";"identificador";"coringa_1";"coringa_2";"data_entrada";"data_abertura";"tipo_dispositivo"';
                } else {
                    $arrayLines[] = '"campanha";"resposta";"telefone";"data_evento";"identificador";"coringa_1";"coringa_2";"data_entrada";"data_abertura";"tipo_dispositivo"';
                }

                $count=0;
                foreach ($returnReportreply['data'] as $key => $row) {
                    if ($request->id_campaign) {
                        $reportreply['data'][$count]['reply'] = $row['reply'];
                        $reportreply['data'][$count]['phone'] = $row['phone'];
                        $reportreply['data'][$count]['date_event'] = $row['date_event'];
                        $reportreply['data'][$count]['identification'] = $row['identification'];
                        $reportreply['data'][$count]['joker_one'] = $row['joker_one'];
                        $reportreply['data'][$count]['joker_two'] = $row['joker_two'];
                        $reportreply['data'][$count]['created_at'] = $row['date_created'];
                        $reportreply['data'][$count]['date_opened'] = $row['date_opened'];
                        $reportreply['data'][$count]['device_type'] = $row['device_type'];
                    } else {
                        $reportreply['data'][$count]['name'] = $row['name'];
                        $reportreply['data'][$count]['reply'] = $row['reply'];
                        $reportreply['data'][$count]['phone'] = $row['phone'];
                        $reportreply['data'][$count]['date_event'] = $row['date_event'];
                        $reportreply['data'][$count]['identification'] = $row['identification'];
                        $reportreply['data'][$count]['joker_one'] = $row['joker_one'];
                        $reportreply['data'][$count]['joker_two'] = $row['joker_two'];
                        $reportreply['data'][$count]['created_at'] = $row['date_created'];
                        $reportreply['data'][$count]['date_opened'] = $row['date_opened'];
                        $reportreply['data'][$count]['device_type'] = $row['device_type'];
                    }

                    $count++;
                }

            }else{ // TALKIP

                if ($request->id_campaign) {
                    $arrayLines[] = '"resposta";"telefone";"data_evento";"data_entrada"';
                } else {
                    $arrayLines[] = '"campanha";"resposta";"telefone";"data_evento";"data_entrada"';
                }

                $count=0;
                foreach ($returnReportreply['data'] as $key => $row) {
                    if ($request->id_campaign) {
                        $reportreply['data'][$count]['reply'] = $row['reply'];
                        $reportreply['data'][$count]['phone'] = $row['phone'];
                        $reportreply['data'][$count]['date_event'] = $row['date_event'];
                        $reportreply['data'][$count]['created_at'] = $row['date_created'];
                    } else {
                        $reportreply['data'][$count]['name'] = $row['name'];
                        $reportreply['data'][$count]['reply'] = $row['reply'];
                        $reportreply['data'][$count]['phone'] = $row['phone'];
                        $reportreply['data'][$count]['date_event'] = $row['date_event'];
                        $reportreply['data'][$count]['created_at'] = $row['date_created'];
                    }

                    $count++;
                }

            }

            if (count($reportreply) > 0) {
                foreach ($reportreply['data'] as $key => $value) {
                    $value['date_event'] = Carbon::createFromDate($value['date_event'])->format('d/m/Y');

                    $value['created_at'] = Carbon::createFromDate($value['created_at'])->format('d/m/Y H:i:s');

                    if(!$validClientJustSMS){ // ZENVIA AND KOLMEYA
                        if ($value['date_opened']) {
                            $value['date_opened'] = Carbon::createFromDate($value['date_opened'])->format('d/m/Y H:i:s');
                        }

                        if ($value['device_type']) {
                            if ($value['device_type'] == 'mobile') {
                                $value['device_type'] = 'celular';
                            }else {
                                $value['device_type'] = 'computador';
                            }
                        }
                    }

                    foreach ($value as $key2 => $value2) {
                        $value[$key2] = "\"" . preg_replace('/\s/',' ',$value2) . "\"";
                    }
                    $arrayLines[] = implode(';', $value);
                }

                $csv = implode("\n", $arrayLines);
                $name = 'resposta';
                if ($request->id_campaign) {
                    $name = $name . '_' . $request->id_campaign;
                    $name = $name . '_' . Carbon::createFromDate($request->init_date)->format('Ymd');
                }else {
                    $name = $name . '_' . Carbon::createFromDate($request->init_date)->format('Ymd');
                    $name = $name . '_' . Carbon::createFromDate($request->end_date)->format('Ymd');
                }
                return ['content' => $csv, 'name' => $name];

            }

            return ['erro' => 1, 'title' => "Vazio!", 'message' => "Nenhum dado encontrado."];


        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDownloadFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

}
