<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\smsController;
use App\Models\Clients;
use App\Models\IndexExport;
use App\Models\ListCustom;
use App\Models\LogErros;
use App\Models\LogUpdate;
use App\Models\LogImportError;
use App\Models\RecordSendedMLGomes;
use App\Models\LogSshExport;
use App\Models\MailingProcess;
use App\Models\BatchSendControl;
use App\Models\ReplySms;
use App\Models\UserClient;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class LibraryController extends Controller
{
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
        // $user_agents = array("iPhone","iPad","Android","webOS","BlackBerry","iPod","Symbian","IsGeneric");
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

    public function errorimport($file_name_genion, $lineFile = '', $fieldsErros = '', $qtdErrors = 0, $id_campaigns = 0, $date_import = '', $userId = null)
    {
        if($userId == null){
            $user = auth('api')->user();
            $userId = $user->id_client;
        }

        $logImportError = new LogImportError;
        $logImportError->id_client = $userId;
        $logImportError->line_file = $lineFile;
        $logImportError->id_campaigns = $id_campaigns;
        $logImportError->qtd_errors = $qtdErrors;
        $logImportError->fields_errors = $fieldsErros;
        $logImportError->name_file = $file_name_genion;
        $logImportError->date_input = now();
        $logImportError->save();
    }

    public static function validateDate($date, $format = 'Y-m-d')
    {
        $dateTime = DateTime::createFromFormat($format, $date);
        $errors = DateTime::getLastErrors();
        if ($errors['warning_count'] != 0 || $errors['error_count'] != 0) {
            return false;
        }
        return true;
    }

    public function removeaccent(String $stripAccents)
    {
        return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $stripAccents);
    }

    public static function logupdate(Model $model): void
    {
        $user = auth('api')->user();
        foreach ($model->getDirty() as $key => $dirting) {
            $log = new LogUpdate;
            $log->id_user = $user->id;
            $log->table = $model->getTable();
            $log->id_register = $model->id;
            $log->field = $key;
            $log->old_value = ($model->getOriginal()[$key] == NULL ? "": $model->getOriginal()[$key]);
            $log->new_value = $model->getDirty()[$key];
            $log->modified_at = now();
            $log->save();
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

    public function validListCustom($value, $key)
    {
        try{

            $errorImport = [
                'fields' => [],
                'line' => 0
            ];
            $count = 0;
            $telefonia = substr($value['phone'], 2,1);
            if (strlen($value['phone']) != 11 || $telefonia != 9) {
                $count++;
                $errorImport['line'] = ($key+1);
                $errorImport['fields'][] = ['field' => "telefone", 'value' => $value['phone']];
            }

            $library = new LibraryController;
            $just_sms = $library->validClientJustSMS();
            if($just_sms){
                $size_sms = strlen(rtrim(trim($value['message_sms'])));
                if ($size_sms > 160) {
                    $count++;
                    $errorImport['fields'][] = ['field' => "mensagem_sms", 'value' => $value['message_sms']];
                }
                return [$errorImport, $count];
            }

            if (!$library->validateDate($value['date_event'])) {
                $count++;
                $errorImport['fields'][] = ['field' => "data_inicio", 'value' => $value['date_event']];
            }
            $size_sms = strlen(trim($value['message_sms']));
            if ($size_sms > 130) {
                $count++;
                $errorImport['fields'][] = ['field' => "mensagem_sms", 'value' => $value['message_sms']];
            }
            $tamanho_titulo = strlen(trim($value['title']));
            if ($tamanho_titulo > 50) {
                $count++;
                $errorImport['fields'][] = ['field' => "titulo_evento", 'value' => $value['title']];
            }
            $tamanho_descricao = strlen(trim($value['description']));
            if ($tamanho_descricao > 300) {
                $count++;
                $errorImport['fields'][] = ['field' => "descricao", 'value' => $value['description']];
            }
            $tamanho_localizacao = strlen(trim($value['location']));
            if ($tamanho_localizacao > 100) {
                $count++;
                $errorImport['fields'][] = ['field' => "localizacao", 'value' => $value['location']];
            }
            $tamanho_identificador = strlen(trim($value['identification']));
            if ($tamanho_identificador > 50) {
                $count++;
                $errorImport['fields'][] = ['field' => "identificador", 'value' => $value['identification']];
            }
            $tamanho_coringa1 = strlen(trim($value['joker_one']));
            if ($tamanho_coringa1 > 50) {
                $count++;
                $errorImport['fields'][] = ['field' => "coringa_1", 'value' => $value['joker_one']];
            }
            $tamanho_coringa2 = strlen(trim($value['joker_two']));
            if ($tamanho_coringa2 > 50) {
                $count++;
                $errorImport['fields'][] = ['field' => "coringa_2", 'value' => $value['joker_two']];
            }
            return [$errorImport, $count];
        }catch (Exception $e) {
            $logSshExport['type'] = 2;
            $logSshExport['message'] = $e->getMessage();
            $logSshExport['description'] = $e->getTraceAsString();
            LogSshExport::create($logSshExport);
        }
    }

    public static function recordError(Exception $error)
    {
        try {
            $me = auth('api')->user();
            $logErro = [
                'Message' => $error->getMessage(),
                'Code' => $error->getCode(),
                'File' => $error->getFile(),
                'Line' => $error->getLine(),
                'TraceAsString' => $error->getTraceAsString(),
                'id_user' => $me->id
            ];
            $logErros = new LogErros;
            $logErros->fill($logErro);
            $logErros->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function uploadSSH()
    {
        $logSshExport = array();
        try {
            $logSshExport['type'] = 1;
            $logSshExport['init_process'] = Carbon::now()->format('Y-m-d H:i:s');
            $init_date = Carbon::now();
            $end_date = Carbon::now();
            $id_client = 8;

            $listCustom = new ListCustom;
            $listCustom = $listCustom->whereBetween('list_custom.created_at', [$init_date->format('Y-m-d') . ' 00:00:00', $end_date->format('Y-m-d') . ' 23:59:59']);
            $listCustom = $listCustom->join('campaigns','list_custom.id_campaign', 'campaigns.id');
            $listCustom = $listCustom->where('list_custom.id_client', $id_client);
            $listCustom = $listCustom->leftJoin('status_links', 'list_custom.id_status_link', 'status_links.id');
            $listCustom = $listCustom->leftJoin('send_sms', 'list_custom.id_send_sms', 'send_sms.id');
            $listCustom = $listCustom->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
            $listCustom = $listCustom->leftJoin('index_exports', 'list_custom.id', 'index_exports.id_list_custom');
            $listCustom = $listCustom->select(DB::raw('list_custom.id id_custom, list_custom.joker_one, campaigns.name as name_campaign,
                                                        CONCAT(list_custom.ddd,list_custom.phone) celular, list_custom.message_sms, list_custom.date_event,
                                                        list_custom.title, list_custom.description, list_custom.location, list_custom.identification,
                                                        list_custom.joker_one, list_custom.joker_two, send_sms.status status, list_custom.id_send_sms id_status_send,
                                                        status_links.name status_link, list_custom.hash, list_custom.created_at input_date, log_link_sms.date_opened,
                                                        log_link_sms.device_type, index_exports.index IDUNICO'));
            $query = vsprintf(str_replace('?', '%s', $listCustom->toSql()), collect($listCustom->getBindings())->map(function ($binding) {
                        return is_numeric($binding) ? $binding : "'{$binding}'";
                    })->toArray());
            $logSshExport['init_search'] = ("Query: $query");
            $listCustom = $listCustom->get()->toArray();

            $total = count($listCustom);
            $logSshExport['end_search'] = "Foram encontrados $total registros";
            $logSshExport['init_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
            $arrayLines[] = 'IDUNICO;DATAINSERT;DATA;HORA;IDCUSTOMER;CPF;CNPJ;CONTRATO;SEGMENTOCANAL;PORTFOLIO;PRODUTOPORTFOLIO;CARTEIRA;PRODUTO;SUBPRODUTO;TIPO DE CANAL;PHONENUMBER;FORNECEDOR;E-MAIL INEXISTENTE;ENTREGA;E-MAIL;RESPOSTA;NAVEGADOR;ABERTO;LIDO;VLRPRINC;VLRATRASO;PARCELAATRASO;CRM;OptinAgenda';
            foreach ($listCustom as $key => $value) {
                $prefix = $value['joker_one'] == 'COB' ? 'C' : 'N';
                $IDUNICO = $prefix . ($value['IDUNICO'] + 1);
                $entrada = ($value['id_status_send'] == '1' ? 1 : 2);
                $crm = $value['joker_one'] == 'COB' ? $value['joker_one'] : 'NEO';
                if (!$value['IDUNICO']) {
                    $ie = IndexExport::where('crm_type', $prefix)->max('index');
                    if ($ie == null && $prefix == 'C') {
                        $ie = env('INDEX_ML_GOMES_C');
                    }
                    if ($ie == null && $prefix == 'N') {
                        $ie = env('INDEX_ML_GOMES_N');
                    }
                    $ie += 1;
                    IndexExport::create([
                        'id_list_custom' => $value['id_custom'],
                        'index' => $ie,
                        'crm_type' => $prefix
                    ]);
                    $value['IDUNICO'] = $ie;
                    $IDUNICO = $prefix . $ie;
                }
                $linha = [];
                $value['hash'] = URL::to('/')."/".$value['hash'];
                $data = Carbon::createFromDate($value['input_date'])->format('d/m/Y');
                $hora = Carbon::createFromDate($value['input_date'])->format('H:i:s');
                $value['input_date'] = Carbon::createFromDate($value['input_date'])->format('d/m/Y H:i:s');
                if (isset($value['date_opened'])) {
                    $value['date_opened'] = Carbon::createFromDate($value['date_opened'])->format('d/m/Y H:i:s');
                }
                $visualizado = 0;
                if (isset($value['device_type'])) {
                    $visualizado = 1;
                    if ($value['device_type'] == 'mobile') {
                        $value['device_type'] = 'celular';
                    }else {
                        $value['device_type'] = 'computador';
                    }
                }
                foreach ($value as $key2 => $value2) {
                    $value[$key2] = preg_replace('/\s/',' ',$value2);
                }
                $linha = [
                    $IDUNICO,
                    $data,
                    $data,
                    $hora,
                    $value['identification'],
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    15,
                    $value['celular'],
                    'Genion',
                    '',
                    $entrada,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    $crm,
                    $visualizado
                ];

                $arrayLines[] = implode(';', $linha);
            }

            $logSshExport['end_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
            $csv = implode("\n", $arrayLines);
            $logSshExport['init_upload'] = Carbon::now()->format('Y-m-d H:i:s');

            Storage::disk('sftpMlGomes')->put("/MLGOMESCampanhas_Multicanal_Optin_{$end_date->format('Ymd')}_1.csv", $csv);

            $logSshExport['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
            LogSshExport::create($logSshExport);
        } catch (Exception $e) {
            $logSshExport['type'] = 2;
            $logSshExport['message'] = $e->getMessage();
            $logSshExport['description'] = $e->getTraceAsString();
            LogSshExport::create($logSshExport);
        }
    }

    public static function newUploadSSH()
    {
        $logSshExport = array();
        $logSshExportBradesco = array();
        try {
            $logSshExport['type'] = 1;
            $logSshExportBradesco['type'] = 1;
            $logSshExport['init_process'] = Carbon::now()->format('Y-m-d H:i:s');
            $logSshExportBradesco['init_process'] = Carbon::now()->format('Y-m-d H:i:s');
            $init_date = Carbon::now();
            $end_date = Carbon::now();
            $id_client = 8;

            $listCustom = new ListCustom;
            $listCustom = $listCustom->whereBetween('list_custom.created_at', [$init_date->format('Y-m-d') . ' 00:00:00', $end_date->format('Y-m-d') . ' 23:59:59']);
            $listCustom = $listCustom->join('campaigns','list_custom.id_campaign', 'campaigns.id');
            $listCustom = $listCustom->where('list_custom.id_client', $id_client);
            $listCustom = $listCustom->leftJoin('status_links', 'list_custom.id_status_link', 'status_links.id');
            $listCustom = $listCustom->leftJoin('send_sms', 'list_custom.id_send_sms', 'send_sms.id');
            $listCustom = $listCustom->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
            $listCustom = $listCustom->leftJoin('index_exports', 'list_custom.id', 'index_exports.id_list_custom');
            $listCustom = $listCustom->select(DB::raw('list_custom.id id_custom, list_custom.joker_one, campaigns.id as id_campaign, campaigns.name as name_campaign,
                                                        CONCAT(list_custom.ddd,list_custom.phone) celular, list_custom.message_sms, list_custom.date_event,
                                                        list_custom.title, list_custom.description, list_custom.location, list_custom.identification,
                                                        list_custom.joker_one, list_custom.joker_two, send_sms.status status, list_custom.id_send_sms id_status_send,
                                                        status_links.name status_link, list_custom.hash, list_custom.created_at input_date, log_link_sms.date_opened,
                                                        log_link_sms.device_type, index_exports.index IDUNICO, list_custom.sended_at'));
            $query = vsprintf(str_replace('?', '%s', $listCustom->toSql()), collect($listCustom->getBindings())->map(function ($binding) {
                        return is_numeric($binding) ? $binding : "'{$binding}'";
                    })->toArray());
            $logSshExport['init_search'] = ("Query: $query");
            $logSshExportBradesco['init_search'] = ("Query: $query");
            $listCustom = $listCustom->get()->toArray();

            $total=0;
            $totalBradesco=0;
            foreach ($listCustom as $row) {
                if ($row['id_campaign'] != "446") {
                    $total = $total + 1;
                }else{
                    $totalBradesco = $totalBradesco + 1;
                }
            }

            $logSshExport['end_search'] = "Foram encontrados $total registros";
            $logSshExportBradesco['end_search'] = "Foram encontrados $totalBradesco registros";
            $logSshExport['name_file'] = "Campanhas_Multicanal_Optin";
            $logSshExportBradesco['name_file'] = "Campanha_Optin_446";
            $logSshExport['init_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
            $logSshExportBradesco['init_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
            $arrayLines[] = 'IDUNICO;DATAINSERT;DATA;HORA;IDCUSTOMER;CPF;CNPJ;CONTRATO;SEGMENTOCANAL;PORTFOLIO;PRODUTOPORTFOLIO;CARTEIRA;PRODUTO;SUBPRODUTO;TIPO DE CANAL;PHONENUMBER;FORNECEDOR;E-MAIL INEXISTENTE;ENTREGA;E-MAIL;RESPOSTA;NAVEGADOR;ABERTO;LIDO;VLRPRINC;VLRATRASO;PARCELAATRASO;CRM;OptinAgenda;Envio;dataacordo;parcela;dataretorno;dataenvio';
            $arrayLinesBradesco[] = 'IDUNICO;DATAINSERT;DATA;HORA;IDCUSTOMER;CPF;CNPJ;CONTRATO;SEGMENTOCANAL;PORTFOLIO;PRODUTOPORTFOLIO;CARTEIRA;PRODUTO;SUBPRODUTO;TIPO DE CANAL;PHONENUMBER;FORNECEDOR;E-MAIL INEXISTENTE;ENTREGA;E-MAIL;RESPOSTA;NAVEGADOR;ABERTO;LIDO;VLRPRINC;VLRATRASO;PARCELAATRASO;CRM;OptinAgenda;Envio;dataacordo;parcela;dataretorno;dataenvio';
            foreach ($listCustom as $key => $value) {
                $prefix = $value['joker_one'] == 'COB' ? 'C' : 'N';
                $IDUNICO = $prefix . ($value['IDUNICO'] + 1);
                $entrada = ($value['id_status_send'] == '1' ? 1 : 2);
                $crm = $value['joker_one'] == 'COB' ? $value['joker_one'] : 'NEO';
                if (!$value['IDUNICO']) {
                    $ie = IndexExport::where('crm_type', $prefix)->max('index');
                    if ($ie == null && $prefix == 'C') {
                        $ie = env('INDEX_ML_GOMES_C');
                    }
                    if ($ie == null && $prefix == 'N') {
                        $ie = env('INDEX_ML_GOMES_N');
                    }
                    $ie += 1;
                    IndexExport::create([
                        'id_list_custom' => $value['id_custom'],
                        'index' => $ie,
                        'crm_type' => $prefix
                    ]);
                    $value['IDUNICO'] = $ie;
                    $IDUNICO = $prefix . $ie;
                }
                $linha = [];
                $value['hash'] = URL::to('/')."/".$value['hash'];
                $data = Carbon::createFromDate($value['input_date'])->format('d/m/Y');
                $hora = Carbon::createFromDate($value['input_date'])->format('H:i:s');
                $value['input_date'] = Carbon::createFromDate($value['input_date'])->format('d/m/Y H:i:s');
                if (isset($value['date_opened'])) {
                    $value['date_opened'] = Carbon::createFromDate($value['date_opened'])->format('d/m/Y H:i:s');
                }
                $visualizado = 0;
                if (isset($value['device_type'])) {
                    $visualizado = 1;
                    if ($value['device_type'] == 'mobile') {
                        $value['device_type'] = 'celular';
                    }else {
                        $value['device_type'] = 'computador';
                    }
                }
                if (isset($value['joker_two']) && $value['joker_two'] != "#N/D") {
                    $dados = explode("|", $value['joker_two']);
                    $data_acordo = $dados[0];
                    $parcela = $dados[1];
                }else{
                    $data_acordo = "";
                    $parcela = "";
                }

                foreach ($value as $key2 => $value2) {
                    $value[$key2] = preg_replace('/\s/',' ',$value2);
                }

                $logRecordSended = new RecordSendedMLGomes;
                $logRecordSended = $logRecordSended->where("identification", "=", $value['identification']);
                $logRecordSended = $logRecordSended->where("phone", "=", $value['celular']);
                $logRecordSended = $logRecordSended->get();

                if (count($logRecordSended) == 0) {
                    $envio = 2;
                    $logRecordSended_add = new RecordSendedMLGomes;
                    $logRecordSended_add->id_list_custom = $value['id_custom'];
                    $logRecordSended_add->identification = $value['identification'];
                    $logRecordSended_add->phone = $value['celular'];
                    $logRecordSended_add->save();
                }else{
                    $envio = 1;
                }

                if ($value['id_campaign'] != "446") {
                    $linha = [
                        $IDUNICO,
                        $data,
                        $data,
                        $hora,
                        $value['identification'],
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        15,
                        $value['celular'],
                        'Genion',
                        '',
                        $entrada,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        $crm,
                        $visualizado,
                        $envio,
                        $data_acordo,
                        $parcela,
                        $value['date_opened'],
                        $value['sended_at']
                    ];

                    $arrayLines[] = implode(';', $linha);

                }else{
                    $bradesco = [
                        $IDUNICO,
                        $data,
                        $data,
                        $hora,
                        $value['identification'],
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        15,
                        $value['celular'],
                        'Genion',
                        '',
                        $entrada,
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        $crm,
                        $visualizado,
                        $envio,
                        $data_acordo,
                        $parcela,
                        $value['date_opened'],
                        $value['sended_at']
                    ];

                    $arrayLinesBradesco[] = implode(';', $bradesco);

                }


            }

            $collection = collect($arrayLines);
            $collectionBradesco = collect($arrayLinesBradesco);

            $count = $collection->count();
            $countBradesco = $collectionBradesco->count();

            $logSshExport['end_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
            $logSshExportBradesco['end_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
            $csv = implode("\n", $arrayLines);
            $csvBradesco = implode("\n", $arrayLinesBradesco);
            $logSshExport['init_upload'] = Carbon::now()->format('Y-m-d H:i:s');
            $logSshExportBradesco['init_upload'] = Carbon::now()->format('Y-m-d H:i:s');

            if ($count > 1) {
                Storage::disk('sftpMlGomes')->put("/MLGOMESCampanhas_Multicanal_Optin_{$end_date->format('Ymd')}_1.csv", $csv);
            }

            if ($countBradesco > 1) {
                Storage::disk('sftpMlGomes')->put("/MLGOMESCampanha_Optin_446_{$end_date->format('Ymd')}_1.csv", $csvBradesco);
            }

            $logSshExport['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
            $logSshExportBradesco['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
            LogSshExport::create($logSshExport);
            LogSshExport::create($logSshExportBradesco);
        } catch (Exception $e) {
            $logSshExport['type'] = 2;
            $logSshExport['message'] = $e->getMessage();
            $logSshExport['description'] = $e->getTraceAsString();
            $logSshExportBradesco['type'] = 2;
            $logSshExportBradesco['message'] = $e->getMessage();
            $logSshExportBradesco['description'] = $e->getTraceAsString();
            LogSshExport::create($logSshExport);
            LogSshExport::create($logSshExportBradesco);
        }
    }

    public static function recentUploadSSH()
    {
        $logSshExport = array();
        $logSshExportBradesco = array();
        try {
            $logSshExport['type'] = 1;
            $logSshExportBradesco['type'] = 1;
            $logSshExport['init_process'] = Carbon::now()->format('Y-m-d H:i:s');
            $logSshExportBradesco['init_process'] = Carbon::now()->format('Y-m-d H:i:s');
            $init_date = Carbon::now()->subDays(1);
            $end_date = Carbon::now()->subDays(1);
            $id_client = 8;

            $nameTable = "list_custom_".$init_date->format('Ymd');

            $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

            if ($query) {

                $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom");
                $listCustom = $listCustom->whereBetween('list_custom.created_at', [$init_date->format('Y-m-d') . ' 00:00:00', $end_date->format('Y-m-d') . ' 23:59:59']);
                $listCustom = $listCustom->join('campaigns','list_custom.id_campaign', 'campaigns.id');
                $listCustom = $listCustom->where('list_custom.id_client', $id_client);
                $listCustom = $listCustom->leftJoin('status_links', 'list_custom.id_status_link', 'status_links.id');
                $listCustom = $listCustom->leftJoin('send_sms', 'list_custom.id_send_sms', 'send_sms.id');
                $listCustom = $listCustom->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
                $listCustom = $listCustom->leftJoin('index_exports', 'list_custom.id', 'index_exports.id_list_custom');
                $listCustom = $listCustom->select(DB::raw('list_custom.id id_custom, list_custom.joker_one, campaigns.id as id_campaign, campaigns.name as name_campaign,
                                                            CONCAT(list_custom.ddd,list_custom.phone) celular, list_custom.message_sms, list_custom.date_event,
                                                            list_custom.title, list_custom.description, list_custom.location, list_custom.identification,
                                                            list_custom.joker_one, list_custom.joker_two, send_sms.status status, list_custom.id_send_sms id_status_send,
                                                            status_links.name status_link, list_custom.hash, list_custom.created_at input_date, log_link_sms.date_opened,
                                                            log_link_sms.device_type, index_exports.index IDUNICO, list_custom.sended_at'));
                $query = vsprintf(str_replace('?', '%s', $listCustom->toSql()), collect($listCustom->getBindings())->map(function ($binding) {
                            return is_numeric($binding) ? $binding : "'{$binding}'";
                        })->toArray());
                $logSshExport['init_search'] = ("Query: $query");
                $logSshExportBradesco['init_search'] = ("Query: $query");
                $listCustom = $listCustom->get()->toArray();

                $total=0;
                $totalBradesco=0;
                foreach ($listCustom as $row) {
                    if ($row->id_campaign != "446") {
                        $total = $total + 1;
                    }else{
                        $totalBradesco = $totalBradesco + 1;
                    }
                }

                $logSshExport['end_search'] = "Foram encontrados $total registros";
                $logSshExportBradesco['end_search'] = "Foram encontrados $totalBradesco registros";
                $logSshExport['name_file'] = "Campanhas_Multicanal_Optin";
                $logSshExportBradesco['name_file'] = "Campanha_Optin_446";
                $logSshExport['init_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExportBradesco['init_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                $arrayLines[] = 'IDUNICO;DATAINSERT;DATA;HORA;IDCUSTOMER;CPF;CNPJ;CONTRATO;SEGMENTOCANAL;PORTFOLIO;PRODUTOPORTFOLIO;CARTEIRA;PRODUTO;SUBPRODUTO;TIPO DE CANAL;PHONENUMBER;FORNECEDOR;E-MAIL INEXISTENTE;ENTREGA;E-MAIL;RESPOSTA;NAVEGADOR;ABERTO;LIDO;VLRPRINC;VLRATRASO;PARCELAATRASO;CRM;OptinAgenda;Envio;dataacordo;parcela;dataretorno;dataenvio';
                $arrayLinesBradesco[] = 'IDUNICO;DATAINSERT;DATA;HORA;IDCUSTOMER;CPF;CNPJ;CONTRATO;SEGMENTOCANAL;PORTFOLIO;PRODUTOPORTFOLIO;CARTEIRA;PRODUTO;SUBPRODUTO;TIPO DE CANAL;PHONENUMBER;FORNECEDOR;E-MAIL INEXISTENTE;ENTREGA;E-MAIL;RESPOSTA;NAVEGADOR;ABERTO;LIDO;VLRPRINC;VLRATRASO;PARCELAATRASO;CRM;OptinAgenda;Envio;dataacordo;parcela;dataretorno;dataenvio';
                foreach ($listCustom as $key => $value) {
                    $prefix = $value->joker_one == 'COB' ? 'C' : 'N';
                    $IDUNICO = $prefix . ($value->IDUNICO + 1);
                    $entrada = ($value->id_status_send == '1' ? 1 : 2);
                    $crm = $value->joker_one == 'COB' ? $value->joker_one : 'NEO';
                    if (!$value->IDUNICO) {
                        $ie = IndexExport::where('crm_type', $prefix)->max('index');
                        if ($ie == null && $prefix == 'C') {
                            $ie = env('INDEX_ML_GOMES_C');
                        }
                        if ($ie == null && $prefix == 'N') {
                            $ie = env('INDEX_ML_GOMES_N');
                        }
                        $ie += 1;
                        IndexExport::create([
                            'id_list_custom' => $value->id_custom,
                            'index' => $ie,
                            'crm_type' => $prefix
                        ]);
                        $value->IDUNICO = $ie;
                        $IDUNICO = $prefix . $ie;
                    }
                    $linha = [];
                    $value->hash = URL::to('/')."/".$value->hash;
                    $data = Carbon::createFromDate($value->input_date)->format('d/m/Y');
                    $hora = Carbon::createFromDate($value->input_date)->format('H:i:s');
                    $value->input_date = Carbon::createFromDate($value->input_date)->format('d/m/Y H:i:s');
                    if (isset($value->date_opened)) {
                        $value->date_opened = Carbon::createFromDate($value->date_opened)->format('d/m/Y H:i:s');
                    }
                    $visualizado = 0;
                    if (isset($value->device_type)) {
                        $visualizado = 1;
                        if ($value->device_type == 'mobile') {
                            $value->device_type = 'celular';
                        }else {
                            $value->device_type = 'computador';
                        }
                    }
                    if (isset($value->joker_two) && $value->joker_two != "#N/D") {
                        $dados = explode("|", $value->joker_two);
                        $data_acordo = $dados[0];
                        $parcela = $dados[1];
                    }else{
                        $data_acordo = "";
                        $parcela = "";
                    }

                    foreach ($value as $key2 => $value2) {
                        $value->$key2 = preg_replace('/\s/',' ',$value2);
                    }

                    if ($value->id_campaign != "446") {
                        $linha = [
                            $IDUNICO,
                            $data,
                            $data,
                            $hora,
                            $value->identification,
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            15,
                            $value->celular,
                            'Genion',
                            '',
                            $entrada,
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $crm,
                            $visualizado,
                            1,
                            $data_acordo,
                            $parcela,
                            $value->date_opened,
                            $value->sended_at
                        ];

                        $arrayLines[] = implode(';', $linha);

                    }else{
                        $bradesco = [
                            $IDUNICO,
                            $data,
                            $data,
                            $hora,
                            $value->identification,
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            15,
                            $value->celular,
                            'Genion',
                            '',
                            $entrada,
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            $crm,
                            $visualizado,
                            1,
                            $data_acordo,
                            $parcela,
                            $value->date_opened,
                            $value->sended_at
                        ];

                        $arrayLinesBradesco[] = implode(';', $bradesco);

                    }


                }

                $collection = collect($arrayLines);
                $collectionBradesco = collect($arrayLinesBradesco);

                $count = $collection->count();
                $countBradesco = $collectionBradesco->count();

                $logSshExport['end_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExportBradesco['end_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                $csv = implode("\n", $arrayLines);
                $csvBradesco = implode("\n", $arrayLinesBradesco);
                $logSshExport['init_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExportBradesco['init_upload'] = Carbon::now()->format('Y-m-d H:i:s');

                if ($count > 1) {
                    Storage::disk('sftpMlGomes')->put("/MLGOMESCampanhas_Multicanal_Optin_{$end_date->format('Ymd')}_1.csv", $csv);
                }

                if ($countBradesco > 1) {
                    Storage::disk('sftpMlGomes')->put("/MLGOMESCampanha_Optin_446_{$end_date->format('Ymd')}_1.csv", $csvBradesco);
                }

                $logSshExport['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExportBradesco['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                LogSshExport::create($logSshExport);
                LogSshExport::create($logSshExportBradesco);

            }else{
                $logSshExport['type'] = 2;
                $logSshExport['message'] = "Nenhum registro encontrado";
                $logSshExport['description'] = "";
                $logSshExportBradesco['type'] = 2;
                $logSshExportBradesco['message'] = "Nenhum registro encontrado";
                $logSshExportBradesco['description'] = "";
                LogSshExport::create($logSshExport);
                LogSshExport::create($logSshExportBradesco);
            }


        } catch (Exception $e) {
            $logSshExport['type'] = 2;
            $logSshExport['message'] = $e->getMessage();
            $logSshExport['description'] = $e->getTraceAsString();
            $logSshExportBradesco['type'] = 2;
            $logSshExportBradesco['message'] = $e->getMessage();
            $logSshExportBradesco['description'] = $e->getTraceAsString();
            LogSshExport::create($logSshExport);
            LogSshExport::create($logSshExportBradesco);
        }
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

    public static function validClientJustSMS()
    {
        try{
            $user = auth('api')->user();
            $clients = new Clients;
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $clients = $clients->whereIn('id', $userClient);
                } else {
                    $clients = $clients->where('id', $user->id_client);
                }
            }
            $clients = $clients->select('id', 'name', 'contact', 'just_sms', 'active')->get();
            if(count($clients) > 1){
                $just_sms = true;
                foreach ($clients as $row) {
                    if($row['just_sms'] == 0){
                        $just_sms = false;
                        break;
                    }
                }
            }else{
                $just_sms = $clients[0]['just_sms'];
            }

            return $just_sms;

        } catch (Exception $e) {
            Log::debug('Log: ' . $e);
        }
    }

    public static function justSmsUpdateIdSendSms()
    {
        try{

            $control = BatchSendControl::where("verified", 0)->get();

            BatchSendControl::where("verified", 0)
                            ->update([
                                "verified" => 2
                            ]);

            $smsController = new smsController;
            $request = new \Illuminate\Http\Request();

            foreach ($control as $value) {

                $request->id = $value->id_sms;

                $statusSmsTalkip = $smsController->statusSmsTalkip($request, 'multiple');

                foreach ($statusSmsTalkip['numbers'] as $key => $row) {

                    $listCustom = DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id_sms', $request->id)
                                ->first();

                    if ($listCustom) {

                        $value = $listCustom;

                        if ($row['status'] == 200) { // ENVIADO

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 1,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else if ($row['status'] == 2) { // ENTREGUE

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 7,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else if ($row['status'] == 3) { // SMS ERRO

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 2,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else if ($row['status'] == 1) { // PROCESSANDO SMS

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 4,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else if ($row['status'] == 7) { // SEM SALDO NA CONTA

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 8,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else if ($row['status'] == 11) { // NUMERO INVALIDO

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 9,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else if ($row['status'] == 12) { // NUMERO BLOQUEADO

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 10,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else if ($row['status'] == 13) { // NUMERO NA LISTA NEGRA

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 13,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else if ($row['status'] == 14) { // MENSAGEM MAL FORMATADA

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 14,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }else { // ERRO

                            DB::connection('mysql2')->table("list_custom_".Carbon::parse($value->created_at)->format('Ymd'))
                                ->where('id', $value->id)
                                ->where('id_sms', $request->id)
                                ->where('mailing_file_original', $value->mailing_file_original)
                                ->where('mailing_file_genion', $value->mailing_file_genion)
                                ->update([
                                    'id_send_sms' => 15,
                                    'id_sms' => $row['id'].'_'.$request->id,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        }
                    }
                }

                BatchSendControl::where("id_sms", $request->id)
                            ->update([
                                "verified" => 1
                            ]);

            }

            return response()->json(LibraryController::responseApi([], 'ok'));

        } catch (Exception $e) {
            LibraryController::recordError($e);
        }
    }

    public static function deleteMailingProcess()
    {
        try{

            $mailing = MailingProcess::where( 'created_at', 'LIKE', '%'.Carbon::now()->subDays(1)->format('Y-m-d').'%')
                                ->where('confirm_imported', 0)
                                ->delete();

            return response()->json(LibraryController::responseApi($mailing, 'ok'));

        } catch (Exception $e) {
            LibraryController::recordError($e);
        }
    }

    public static function validJustSMS($id_client)
    {
        try{

            $clients = new Clients;
            $clients = $clients->select('just_sms');
            $clients = $clients->where("id", $id_client);
            $clients = $clients->get()->toArray();

            if(count($clients) > 0){
                foreach ($clients as $row) {
                    if($row['just_sms'] == 0){
                        $just_sms = false;
                    }else{
                        $just_sms = true;
                    }
                }
            }else{
                $just_sms = false;
            }

            return $just_sms;

        } catch (Exception $e) {
            Log::debug('Log: ' . $e);
        }
    }

    public static function validationDDD($ddd)
    {

        $array_ddd = array(
                            "68" => 68,
                            "82" => 82,
                            "92"=> 92, "97" => 97,
                            "96" => 96,
                            "71" => 71, "73" => 73, "74" => 74, "75" => 75, "77" => 77,
                            "85" => 85, "88" => 88,
                            "61" => 61,
                            "27" => 27, "28" => 28,
                            "62" => 62, "64" => 64,
                            "98" => 98, "99" => 99,
                            "31" => 31, "32" => 32, "33" => 33, "34" => 34, "35" => 35, "37" => 37, "38" => 38,
                            "67"=> 67,
                            "65" => 65, "66" => 66,
                            "91" => 91, "93" => 93, "94" => 94,
                            "83" =>83,
                            "81" => 81, "87" => 87,
                            "86" => 86, "89" => 89,
                            "41" => 41, "42" => 42, "43" => 43, "44" => 44, "45" => 45, "46" => 46,
                            "21" => 21, "22" => 22, "24" => 24,
                            "84" => 84,
                            "69" => 69,
                            "95" => 95,
                            "51" => 51, "53" => 53, "54" => 54, "55" => 55,
                            "47" => 47, "48" => 48, "49" => 49,
                            "79" => 79,
                            "11" => 11, "12" => 12, "13" => 13, "14" => 14, "15" => 15, "16" => 16, "17" => 17, "18" => 18, "19" => 19,
                            "63" => 63
                    );

        return array_key_exists($ddd, $array_ddd);
    }
}
