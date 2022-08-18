<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Jobs\QueueJobs;
use App\Jobs\SendSmsJob;
use App\Models\Campaigns;
use App\Models\ListCustom;
use App\Models\LogImportError;
use App\Models\UserClient;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListCustomClientController extends Controller
{
    public function upload(Request $request)
    {
        $listCustomController = new ListCustomController;
        $validateListCustom = $listCustomController->validateListCustom($request)->original['data'];
        $uploadfilecustom = $this->uploadfilecustomclient($request)->original;

        return LibraryController::responseApi(['validate' => $validateListCustom, 'statusUpload' => $uploadfilecustom]);
    }

    public function uploadfilecustomclient(Request $request)
    {
        try {
            $client = auth('apiclient')->user();
            $campaignsCount = Campaigns::where('id', $request->campaign);
            $campaignsCount = $campaignsCount->where('id_client', $client->id);
            $campaignsCount = $campaignsCount->get()->count();
            if ($campaignsCount == 0) {
                return response()->json(LibraryController::responseApi([],'Campaign not found', 100));
            }

            $limitQueue = env('LIMIT_QUEUE_SMS');
            for ($i=0; $i < count($request->custom); $i++) {
                $custom = array();
                $custom = array_slice($request->custom, $i, $limitQueue);
                $queue['campaign'] = $request->campaign;
                $queue['send_sms'] = $request->send_sms;
                $queue['custom'] = $custom;
                $i += $limitQueue - 1;

                $nameHash = $request->campaign. Carbon::now()->format('YmdHis');
                $hashQueue = hash("crc32",$nameHash);
                SendSmsJob::dispatch($queue, 0, $client)->onQueue($hashQueue);
                QueueJobs::dispatch($hashQueue);
            }

            return response()->json(LibraryController::responseApi([], 'ok'));
        } catch (Exception $e) {
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    public function report(Request $request)
    {
        try {

            $init_date = new DateTime($request->init_date);
            $end_date = new DateTime($request->end_date);

            $dateRange = array();
            while($init_date <= $end_date){
                $dateRange[] = $init_date->format('Ymd');
                $init_date = $init_date->modify('+1day');
            }

            $nameTable = "";
            $responseListCustom = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom");
                    $client = auth('apiclient')->user();
                    $libraryController = new LibraryController;
                    if (isset($request->init_date)) {
                        if (!$libraryController->validateDate($request->init_date)) {
                            return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                        }
                    }else {
                        $request->merge([
                            'init_date' => Carbon::now()->format('Y-m-d')
                        ]);
                    }
                    if (isset($request->end_date)) {
                        if (!$libraryController->validateDate($request->end_date)) {
                            return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                        }
                    }else {
                        $request->merge([
                            'end_date' => Carbon::now()->format('Y-m-d')
                        ]);
                    }
                    $data1 = Carbon::createFromDate($request->init_date);
                    $data2 = Carbon::createFromDate($request->end_date);
                    if ($data2->lt($data1)) {
                        return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                    }
                    $listCustom = $listCustom->where(function($query) use ($request) {
                        $query->where('list_custom.created_at', '>=', $request->init_date . ' 00:00:00')
                            ->where('list_custom.created_at', '<=', $request->end_date . ' 23:59:59');
                    });
                    $listCustom = $listCustom->join('campaigns','list_custom.id_campaign', 'campaigns.id');
                    $listCustom = $listCustom->where('list_custom.id_client', $client->id);

                    $listCustom = $listCustom->leftJoin('status_links', 'list_custom.id_status_link', 'status_links.id');
                    if (isset($request->id_campaign)) {
                        $listCustom = $listCustom->where('list_custom.id_campaign', $request->id_campaign);
                        $listCustom = $listCustom->leftJoin('send_sms', 'list_custom.id_send_sms', 'send_sms.id');
                        $listCustom = $listCustom->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
                        $listCustom = $listCustom->select(DB::raw('CONCAT(list_custom.ddd,list_custom.phone) celular, list_custom.message_sms, list_custom.date_event, list_custom.title, list_custom.description, list_custom.location, list_custom.identification, list_custom.joker_one, list_custom.joker_two, send_sms.status, status_links.name status_link, list_custom.hash, list_custom.created_at input_date, log_link_sms.date_opened, log_link_sms.device_type'));
                    }else {
                        $listCustom = $listCustom->leftJoin('send_sms', 'list_custom.id_send_sms', 'send_sms.id');
                        $listCustom = $listCustom->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
                        $listCustom = $listCustom->select(DB::raw('campaigns.name as name_campaign, CONCAT(list_custom.ddd,list_custom.phone) celular, list_custom.message_sms, list_custom.date_event, list_custom.title, list_custom.description, list_custom.location, list_custom.identification, list_custom.joker_one, list_custom.joker_two, send_sms.status, status_links.name status_link, list_custom.hash, list_custom.created_at input_date, log_link_sms.date_opened, log_link_sms.device_type'));
                    }

                    $responseListCustom[] = $listCustom->get();
                }
            }

            $return = array();
            foreach ($responseListCustom as $key => $value) {
                $return[] = $value;
            }

            return LibraryController::responseApi($return);
        } catch (Exception $e) {
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    public function reporterrors(Request $request)
    {
        try {
            $client = auth('apiclient')->user();
            $logImportError = new LogImportError;
            $libraryController = new LibraryController;
            if (isset($request->init_date)) {
                if (!$libraryController->validateDate($request->init_date)) {
                    return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                }
            }else {
                $request->merge([
                    'init_date' => Carbon::now()->format('Y-m-d')
                ]);
            }
            if (isset($request->end_date)) {
                if (!$libraryController->validateDate($request->end_date)) {
                    return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                }
            }else {
                $request->merge([
                    'end_date' => Carbon::now()->format('Y-m-d')
                ]);
            }
            $data1 = Carbon::createFromDate($request->init_date);
            $data2 = Carbon::createFromDate($request->end_date);
            if ($data2->lt($data1)) {
                return response()->json(LibraryController::responseApi([], 'invalid date', 100));
            }
            $logImportError = $logImportError->where(function($query) use ($request) {
                $query->where('log_import_errors.created_at', '>=', $request->init_date . ' 00:00:00')
                      ->where('log_import_errors.created_at', '<=', $request->end_date . ' 23:59:59');
            });
            if (isset($request->id_campaign)) {
                $logImportError = $logImportError->where('log_import_errors.id_campaigns', $request->id_campaign);
            }
            $logImportError = $logImportError->where('campaigns.id_client', $client->id);

            $logImportError = $logImportError->leftJoin('campaigns', 'log_import_errors.id_campaigns', '=', 'campaigns.id');
            $logImportError = $logImportError->select('log_import_errors.id_client',
                                                        'log_import_errors.id_campaigns',
                                                        'log_import_errors.line_file',
                                                        'log_import_errors.name_file',
                                                        'log_import_errors.qtd_errors',
                                                        'log_import_errors.fields_errors',
                                                        'log_import_errors.date_input',
                                                        'campaigns.name as name_campaign');
            $logImportError = $logImportError->get();
            return LibraryController::responseApi($logImportError);
        } catch (Exception $e) {
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

}
