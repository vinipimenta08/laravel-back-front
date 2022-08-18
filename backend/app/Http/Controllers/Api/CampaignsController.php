<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\Campaigns;
use App\Models\CheckExistsWhatsApp;
use App\Models\ListCustom;
use App\Models\LogImportError;
use App\Models\ReplySms;
use App\Models\StartStopSms;
use App\Models\UserClient;
use App\Models\ValueFire;
use App\Models\CustomerBilling;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($data = null)
    {
        try {
            $user = auth('api')->user();
            $campaigns = new Campaigns;

            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $campaigns = $campaigns->whereIn('id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $campaigns = $campaigns->where('id_client', $user->id_client);
                }
            }
            $campaigns = $campaigns->orderBy('name', 'asc')->get();
            if ($data) {
                return $campaigns;
            }
            return response()->json(LibraryController::responseApi($campaigns));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
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
            $user = auth('api')->user();
            $campaigns = new Campaigns;
            $campaigns->fill($request->all());
            if (!$user->alternative_profile) {
                $campaigns->id_client = $user->id_client;
            }
            $campaigns->save();
            return response()->json(LibraryController::responseApi($campaigns, 'ok'));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
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
        try {
            $user = auth('api')->user();
            $campaigns = Campaigns::findOrFail($id);


            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $campaigns = $campaigns->whereIn('id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $campaigns = $campaigns->where('id_client', $user->id_client);
                }

            }
            $campaigns = $campaigns->where('id', $id)->get();
            return response()->json(LibraryController::responseApi($campaigns));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
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
        try {
            $user = auth('api')->user();
            $campaigns = new Campaigns;
            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $campaigns = $campaigns->whereIn('id_client', $userClient);
            } else {
                if ($user->id_profile != 1) {
                    $campaigns = $campaigns->where('id_client', $user->id_client);
                }
            }
            $campaigns = $campaigns->findOrFail($id);
            $campaigns->fill($request->all());
            LibraryController::logupdate($campaigns);
            $campaigns->save();
            return response()->json(LibraryController::responseApi($campaigns, 'ok'));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = auth('api')->user();
            $campaigns = new Campaigns;
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $campaigns = $campaigns->whereIn('id_client', $userClient);
                } else {
                    $campaigns = $campaigns->where('id_client', $user->id_client);
                }
            }
            $campaigns = $campaigns->findOrFail($id);
            $campaigns->delete();
            return response()->json(LibraryController::responseApi($campaigns, 'ok'));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }

    }

    public function statuscampaign(Request $request)
    {
        $library = new LibraryController;
        if (isset($request->init_date) || isset($request->end_date)) {
            if (!$library->validateDate($request->init_date)) {
                return response()->json($library->responseApi([],'invalid date', '100'));
            }
            if (!$library->validateDate($request->end_date)) {
                return response()->json($library->responseApi([],'invalid date', '100'));
            }
        }
        $data1 = Carbon::createFromDate($request->init_date);
        $data2 = Carbon::createFromDate($request->end_date);
        if ($data2->lt($data1)) {
            return response()->json(LibraryController::responseApi([], 'invalid date', '100'));
        }

        try {
            $user = auth('api')->user();
            $campaigns = new Campaigns;
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $campaigns = $campaigns->whereIn('id_client', $userClient);
                } else {
                    $campaigns = $campaigns->where('id_client', $user->id_client);
                }
            }else {
                if (isset($request->id_client)) {
                    $campaigns = $campaigns->where('id_client', $request->id_client);
                }
            }
            $campaigns = $campaigns->select('id', 'id_client', 'name', 'active')->get();
            $response = [];
            foreach ($campaigns as $key => $campaign) {
                $countLogImportError = LogImportError::where('id_campaigns', $campaign->id);
                if (isset($request->init_date) && isset($request->end_date)) {
                    $countLogImportError = $countLogImportError->where(function($query) use ($request){
                        $query->where('created_at', '>=', $request->init_date . ' 00:00:00')
                              ->where('created_at', '<=', $request->end_date . ' 23:59:59');
                    });
                }

                $init_date = new DateTime($request->init_date);
                $end_date = new DateTime($request->end_date);

                $dateRange = array();
                while($init_date <= $end_date){
                    $dateRange[] = $init_date->format('Ymd');
                    $init_date = $init_date->modify('+1day');
                }

                $listcustom = array();
                foreach ($dateRange as $row) {
                    $nameTable = "list_custom_".$row;

                    $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                    if ($query) {
                        $listcustom[] = DB::connection('mysql2')->table($nameTable)
                                    ->where('id_campaign', $campaign->id)
                                    ->get()
                                    ->toArray();
                    }
                }

                $result = array();
                foreach ($listcustom as $key => $value) {
                    foreach ($value as $row) {
                        $result[] = $row;
                    }
                }
                $countLogImportError = $countLogImportError->count();

                $sended = 0;
                $delivered = 0;
                $errorSend = 0;
                $link = 0;
                $error = 0;
                $processing = 0;
                foreach ($result as $key => $value) {
                    if ($value->id_send_sms == 4) {
                        $processing++;
                    }
                    if ($value->id_send_sms == 3) {
                        $link++;
                    }
                    if ($value->id_send_sms == 2) {
                        $errorSend++;
                    }
                    if ($value->id_send_sms == 1) {
                        $sended++;
                    }
                    if ($value->id_send_sms == 7) {
                        $delivered++;
                    }
                }
                $sendedValues = 0;
                $foreseenSendedValues = 0;
                $hashQueue = hash("crc32",$campaign->id);

                $total = $sended + $delivered;

                $startStopSms = StartStopSms::where('hash_id_campaign', $hashQueue)->first();
                $sendedValue = ValueFire::where('qtd_min', '<=', $total)
                                        ->where('qtd_max', '>=', $total)->first();
                $pricing = CustomerBilling::where('active', '=', 1)->get();

                foreach ($pricing as $row) {
                    if ($row->id_client == $campaign->id_client) {
                        $sendedValue['value'] = $row->value;
                    }
                }

                $sendedValues = isset($sendedValue['value']) ? $sendedValue['value'] : 0;
                $valueSended = ($sendedValues * $total);

                $foreseenSended = ($link + $errorSend + $sended + $processing + $delivered);
                $foreseenSendedValue = ValueFire::where('qtd_min', '<=', $foreseenSended)
                                        ->where('qtd_max', '>=', $foreseenSended)->first();
                $pricing = CustomerBilling::where('active', '=', 1)->get();

                foreach ($pricing as $row) {
                    if ($row->id_client == $campaign->id_client) {
                        $foreseenSendedValue['value'] = $row->value;
                    }
                }

                $foreseenSendedValues = isset($foreseenSendedValue['value']) ? $foreseenSendedValue['value'] : 0;
                $foreseen = (($sended + $errorSend + $link + $processing + $delivered) * $foreseenSendedValues);


                if ($sended || $errorSend || $link || $countLogImportError || $processing || $delivered) {
                    $response[] = [
                        'campaigns' => $campaign,
                        'sended' => $sended,
                        "delivered" => $delivered,
                        'errorSend' => $errorSend,
                        'processing' => $processing,
                        'link' => $link,
                        'error' => $countLogImportError,
                        'runing' => isset($startStopSms) ? $startStopSms->run : 0,
                        'valueSended' => $valueSended,
                        'foreseen' => $foreseen,
                    ];
                }
            }
            return response()->json(LibraryController::responseApi($response));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    public function active_cost_center(Request $request)
    {
        try {
            $campaigns = Campaigns::findOrFail($request->id);
            $campaigns->active = $request->active;
            LibraryController::logupdate($campaigns);
            $campaigns->update();
            return response()->json(LibraryController::responseApi([], "OK"));
        } catch (Exception $e) {
            LibraryController::recordError($e);
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
            $resultCampaigns = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    $user = auth('api')->user();
                    $campaigns = new Campaigns;
                    if ($user->alternative_profile) {
                        $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                        $campaigns = $campaigns->whereIn('campaigns.id_client', $userClient);
                    } else {
                        if ($user->id_profile != 1) {
                            $campaigns = $campaigns->where('campaigns.id_client', $user->id_client);
                        }
                    }
                    if (isset($request->id_campaign)) {
                        $campaigns = $campaigns->where('campaigns.id', $request->id_campaign);
                    }

                    $nameTable = "list_custom_".$row;

                    $campaigns = $campaigns->whereNotNull($nameTable.'.created_at');

                    $campaigns = $campaigns->leftJoin($nameTable, function($join) use($nameTable){
                        $join->on('campaigns.id','=', $nameTable.".id_campaign");
                    });

                    $campaigns = $campaigns->select('campaigns.id' ,'campaigns.name', DB::raw("DATE(".$nameTable.".created_at) AS created_at"))->distinct();

                    $campaigns = $campaigns->groupBy('campaigns.id', 'campaigns.name', $nameTable.".created_at");

                    $resultCampaigns[] = $campaigns->get();

                }

            }

            $return = array();
            foreach ($resultCampaigns as $key => $value) {
                $return[] = $value;
            }

            return LibraryController::responseApi($return);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    public function reportlist(Request $request)
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
            $resultListCustom = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $date = Carbon::parse($row)->format("Y-m-d");

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    $listCustom = DB::connection('mysql2')->table($nameTable." AS list_custom");
                    $user = auth('api')->user();
                    $listCustom = $listCustom->where(DB::raw("DATE(list_custom.created_at)"), $date);
                    $listCustom = $listCustom->join('campaigns','list_custom.id_campaign', 'campaigns.id');

                    if ($user->id_profile != 1) {
                        if ($user->alternative_profile) {
                            $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                            $listCustom = $listCustom->whereIn('list_custom.id_client', $userClient);
                        } else {
                            $listCustom = $listCustom->where('list_custom.id_client', $user->id_client);
                        }
                    }

                    $listCustom = $listCustom->leftJoin('status_links', 'list_custom.id_status_link', 'status_links.id');
                    if (isset($request->id_campaign)) {
                        $listCustom = $listCustom->where('list_custom.id_campaign', $request->id_campaign);
                        $listCustom = $listCustom->leftJoin('send_sms', 'list_custom.id_send_sms', 'send_sms.id');
                        $listCustom = $listCustom->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
                        $listCustom = $listCustom->leftJoin('check_exists_whatsApp', 'list_custom.id', 'check_exists_whatsApp.id_genion');
                        $listCustom = $listCustom->select(DB::raw('CONCAT(list_custom.ddd,list_custom.phone) celular, list_custom.message_sms, list_custom.date_event, list_custom.title, list_custom.description, list_custom.location, list_custom.identification, list_custom.joker_one, list_custom.joker_two, send_sms.status, status_links.name status_link, list_custom.hash, list_custom.created_at input_date, log_link_sms.date_opened, log_link_sms.device_type, check_exists_whatsApp.phone as whatsApp'));
                    }else {
                        $listCustom = $listCustom->leftJoin('send_sms', 'list_custom.id_send_sms', 'send_sms.id');
                        $listCustom = $listCustom->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
                        $listCustom = $listCustom->leftJoin('check_exists_whatsApp', 'list_custom.id', 'check_exists_whatsApp.id_genion');
                        $listCustom = $listCustom->select(DB::raw('campaigns.name as name_campaign, CONCAT(list_custom.ddd,list_custom.phone) celular, list_custom.message_sms, list_custom.date_event, list_custom.title, list_custom.description, list_custom.location, list_custom.identification, list_custom.joker_one, list_custom.joker_two, send_sms.status, status_links.name status_link, list_custom.hash, list_custom.created_at input_date, log_link_sms.date_opened, log_link_sms.device_type, check_exists_whatsApp.phone as whatsApp'));
                    }

                    $resultListCustom[] = $listCustom->get()->toArray();

                }
            }

            $return = array();
            foreach ($resultListCustom as $key => $value) {
                foreach ($value as $row) {
                    if ($row->whatsApp) {
                        $row->whatsApp = 1;
                    }else{
                        $row->whatsApp = 0;
                    }
                    $return[] = $row;
                }
            }

            return LibraryController::responseApi($return);
        } catch (Exception $e) {
            LibraryController::recordError($e);
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
            $user = auth('api')->user();
            $logImportError = new LogImportError;
            $logImportError = $logImportError->where(function($query) use ($request) {
                $query->where('log_import_errors.created_at', '>=', $request->init_date . ' 00:00:00')
                      ->where('log_import_errors.created_at', '<=', $request->end_date . ' 23:59:59');
            });
            if (isset($request->id_campaign)) {
                $logImportError = $logImportError->where('log_import_errors.id_campaigns', $request->id_campaign);
            }
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $logImportError = $logImportError->whereIn('campaigns.id_client', $userClient);
                } else {
                    $logImportError = $logImportError->where('campaigns.id_client', $user->id_client);
                }
            }
            $logImportError = $logImportError->leftJoin('campaigns', 'log_import_errors.id_campaigns', '=', 'campaigns.id');
            $logImportError = $logImportError->select('log_import_errors.*', 'campaigns.name as name_campaign');
            $logImportError = $logImportError->get();
            return LibraryController::responseApi($logImportError);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    public function reportreply(Request $request)
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
            $resultReplySms = array();
            foreach ($dateRange as $row) {

                $nameTable = "list_custom_".$row;

                $date = Carbon::parse($row)->format("Y-m-d");

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    $user = auth('api')->user();
                    $replySms = new ReplySms;

                    if ($user->id_profile != 1) {
                        if ($user->alternative_profile) {
                            $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                            $replySms = $replySms->whereIn('list_custom.id_client', $userClient);
                        } else {
                            $replySms = $replySms->where('list_custom.id_client', $user->id_client);
                        }
                    }

                    if (isset($request->id_campaign)) {
                        $replySms = $replySms->join($nameTable.' AS list_custom', 'list_custom.id_sms', '=', 'reply_sms.id_sms');
                        $replySms = $replySms->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
                        $replySms = $replySms->select("reply_sms.reply", "reply_sms.phone", "list_custom.date_event", "list_custom.identification", "list_custom.joker_one", "list_custom.joker_two", "list_custom.created_at AS date_created", "log_link_sms.date_opened", "log_link_sms.device_type");
                        $replySms = $replySms->where(DB::raw("DATE(reply_sms.created_at)"), $date);
                        $replySms = $replySms->where('list_custom.id_campaign', $request->id_campaign);
                        $replySms = $replySms->orderBy("reply_sms.id_list_custom");
                    }else{
                        $replySms = $replySms->join($nameTable.' AS list_custom', 'list_custom.id_sms', '=', 'reply_sms.id_sms');
                        $replySms = $replySms->join('campaigns', 'campaigns.id', '=', 'list_custom.id_campaign');
                        $replySms = $replySms->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom');
                        $replySms = $replySms->select("campaigns.name", "reply_sms.reply", "reply_sms.phone", "list_custom.date_event", "list_custom.identification", "list_custom.joker_one", "list_custom.joker_two", "list_custom.created_at AS date_created", "log_link_sms.date_opened", "log_link_sms.device_type");
                        $replySms = $replySms->where(DB::raw("DATE(reply_sms.created_at)"), $date);
                        $replySms = $replySms->orderBy("reply_sms.id_list_custom");
                    }

                    $resultReplySms[] = $replySms->get()->toArray();

                }

            }

            $return = array();
            foreach ($resultReplySms as $value) {
                foreach ($value as $row) {
                    $return[] = $row;
                }
            }

            return LibraryController::responseApi($return);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }
}
