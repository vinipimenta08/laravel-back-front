<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Jobs\ImportMailingJob;
use App\Jobs\QueueJobs;
use App\Models\Campaigns;
use App\Models\ListCustom;
use App\Models\LogSshExport;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class SftpClients extends Controller
{

    public function importMailingLocalCred()
    {
        $logSshExport = array();
        try{
            $logSshExport['init_process'] = Carbon::now()->format('Y-m-d H:i:s');
            $id_client = 7;
            $dr_imports = 'optin/imports/';
            $files = Storage::disk('sftpLocalcred')->Files($dr_imports);

            if(count($files) > 0){
                foreach ($files as $file) {

                    $path_name = explode("/", $file);
                    $id_campaign = explode(".", $path_name[2]);
                    $id_campaign = $id_campaign[0];
                    $Campaigns = Campaigns::where('campaigns.id',  $id_campaign)->get()->toArray();
                    if(count($Campaigns) == 0){
                        $Campaigns_new = Campaigns::where('campaigns.id_client', $id_client)->where('name', 'IMPORTAÇÃO AUTOMATICA')->get()->toArray();
                        if(count($Campaigns_new) == 0){
                            $campaigns = new Campaigns();
                            $campaigns->id_client = $id_client;
                            $campaigns->name = 'IMPORTAÇÃO AUTOMATICA';
                            $campaigns->save();
                            $id_campaign = $campaigns->id;
                        }else{
                            $id_campaign = $Campaigns_new[0]['id'];
                        }
                    }

                    $contents = Storage::disk('sftpLocalcred')->get($file);
                    $object =  explode("\n", $contents);

                    $lines = [];
                    $custom = [];
                    foreach ($object as $dados) {
                        $lines[] = explode(";", $dados);
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

                    $queue['campaign'] = $id_campaign;
                    $queue['send_sms'] = 1;
                    $queue['custom'] = $custom;

                    $user['id'] = 1;
                    $nameHash = $id_campaign. Carbon::now()->format('YmdHsi');
                    $hashQueue = hash("crc32",$nameHash);
                    ImportMailingJob::dispatch($queue, 0, $user)->onQueue($hashQueue);
                    QueueJobs::dispatch($hashQueue);

                    $name_file =  explode(".", $file);
                    $name_file = $name_file[0] . '_' . Carbon::now()->format('dmY_His') . '.' . $name_file[1];
                    $file_old = str_replace("imports", "old", $name_file);
                    Storage::disk('sftpLocalcred')->move($file, $file_old);
                }

                $logSshExport['type'] = 1;
                $logSshExport['message'] = 'importMailingLocalCred';
                $logSshExport['description'] = 'total de arquivos '. COUNT($files);
                $logSshExport['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                LogSshExport::create($logSshExport);
            }
        } catch (Exception $e) {
            $logSshExport['type'] = 2;
            $logSshExport['message'] = $e->getMessage();
            $logSshExport['description'] = $e->getTraceAsString();
            $logSshExport['name_file'] = 'importMailingLocalCred';
            LogSshExport::create($logSshExport);
        }
    }

    public function exportReportsLocalCred()
    {
        $logSshExport = array();
        try{
            $logSshExport['init_process'] = Carbon::now()->format('Y-m-d H:i:s');

            $init_date = Carbon::now();
            $end_date = Carbon::now();

            $id_client =7;

            $nameTable = "list_custom_".$init_date->format('Ymd');

            $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

            if ($query) {

                $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom");
                $listCustom = $listCustom->whereBetween('list_custom.created_at',  [$init_date->format('Y-m-d') . ' 00:00:00', $end_date->format('Y-m-d') . ' 23:59:59']);
                $listCustom = $listCustom->where('list_custom.id_client', $id_client);
                $listCustom = $listCustom->join('campaigns', 'list_custom.id_campaign', 'campaigns.id');
                $listCustom = $listCustom->leftJoin('status_links', 'list_custom.id_status_link', 'status_links.id');
                $listCustom = $listCustom->leftJoin('send_sms', 'list_custom.id_send_sms', 'send_sms.id');
                $listCustom = $listCustom->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
                $listCustom = $listCustom->select(DB::raw('campaigns.name as name_campaign, CONCAT(list_custom.ddd, list_custom.phone) celular, list_custom.message_sms, list_custom.date_event, list_custom.title, list_custom.description, list_custom.location, list_custom.identification, list_custom.joker_one, list_custom.joker_two, send_sms.status, status_links.name status_link, list_custom.hash, list_custom.created_at input_date, log_link_sms.date_opened, log_link_sms.device_type'));

                $query = vsprintf(str_replace('?', '%s', $listCustom->toSql()), collect($listCustom->getBindings())->map(function ($binding) {
                            return is_numeric($binding) ? $binding : "'{$binding}'";
                        })->toArray());
                $logSshExport['init_search'] = ("Query: $query");
                $logSshExportBradesco['init_search'] = ("Query: $query");
                $listCustom = $listCustom->get()->toArray();

                if(COUNT($listCustom) == 0){
                    $logSshExport['end_search'] = "Foram encontrados ". COUNT($listCustom). " registros";
                    $logSshExport['init_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                    $logSshExport['name_file'] = "Campanhas_Localcred_Optin";
                    $logSshExport['end_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                    $logSshExport['init_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                    $logSshExport['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                    LogSshExport::create($logSshExport);
                    return false;
                }
                $arrayLines[] = '"campanha";"telefone";"mensagem_sms";"data_evento";"titulo";"descricao";"localizacao";"identificador";"coringa_1";"coringa_2";"status_sms";"status_link";"link";"data_entrada";"data_abertura";"tipo_dispositivo"';

                foreach ($listCustom as $key => $value) {
                    $value['hash'] =  env('URL_SHORTENER_'.env('ENVIRONMENT')) . $value['hash'];
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

                    foreach ($value as $key2 => $value2) {
                        $value[$key2] = "\"" . preg_replace('/\s/',' ',$value2) . "\"";
                    }

                    $linha = [
                        $value['name_campaign'],
                        $value['celular'],
                        $value['message_sms'],
                        $value['date_event'],
                        $value['title'],
                        $value['description'],
                        $value['location'],
                        $value['identification'],
                        $value['joker_one'],
                        $value['joker_two'],
                        $value['status'],
                        $value['status_link'],
                        $value['hash'],
                        $value['input_date'],
                        $value['date_opened'],
                        $value['device_type']

                    ];
                    $arrayLines[] = implode(';', $linha);
                }

                $total=0;
                foreach ($listCustom as $row) {
                    $total = $total + 1;
                }

                $logSshExport['end_search'] = "Foram encontrados $total registros";
                $logSshExport['init_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExport['name_file'] = "Campanhas_Localcred_Optin";
                $logSshExport['end_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExport['init_upload'] = Carbon::now()->format('Y-m-d H:i:s');

                $csv = implode("\n", $arrayLines);
                Storage::disk('sftpLocalcred')->put("/optin/reports/Campanhas_Localcred_Optin_{$end_date->format('Ymd')}.csv", $csv);

                $logSshExport['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                LogSshExport::create($logSshExport);

            }else{
                $logSshExport['end_search'] = "Foram encontrados 0 registros";
                $logSshExport['init_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExport['name_file'] = "Campanhas_Localcred_Optin";
                $logSshExport['end_make_file'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExport['init_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                $logSshExport['end_upload'] = Carbon::now()->format('Y-m-d H:i:s');
                LogSshExport::create($logSshExport);
                return false;
            }

        }catch (Exception $e) {
            $logSshExport['type'] = 2;
            $logSshExport['message'] = $e->getMessage();
            $logSshExport['description'] = $e->getTraceAsString();
            LogSshExport::create($logSshExport);
        }
        return true;
    }


}
