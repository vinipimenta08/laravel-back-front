<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\Campaigns;
use App\Models\Clients;
use App\Models\HistListCustom;
use App\Models\ListCustom;
use App\Models\LogImportError;
use App\Models\ReplySms;
use App\Models\UserClient;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;
use Illuminate\Http\Request;

class HistListCustomController extends Controller
{

    public static function histdash()
    {
        $request = new StdClass();
        $request->date_init = Carbon::now()->format('Y-m-d');
        $request->date_end = Carbon::now()->format('Y-m-d');

        $card = HistListCustomController::openandreplysended($request);
        $listCustom = HistListCustomController::statuscustom($request);
        $lastDays = HistListCustomController::lastdays($request);
        $greateropening = HistListCustomController::greateropening($request);

        $dashdata = [
                    'card' => $card->original,
                    'listCustom' => $listCustom->original,
                    'lastDays' => $lastDays->original,
                    'greateropening' => $greateropening->original
        ];

        $card = $dashdata['card'];
        $listCustom = $dashdata['listCustom'];
        $lastDays = $dashdata['lastDays'];
        $states = $dashdata['greateropening'];

        $location = array();
        foreach ($states['data'] as $key => $row) {
            foreach ($row as $value) {
                $location[$value['id_campaign']]['id_client'] = $value['id_client'];
                if(!isset($location[$value['id_campaign']]['location'])){
                    $location[$value['id_campaign']]['location'] = $value['estado']."_".$value['total']."|";
                }else{
                    $location[$value['id_campaign']]['location'] .= $value['estado']."_".$value['total']."|";
                }

                if(!isset($location[$value['id_campaign']]['total'])){
                    $location[$value['id_campaign']]['total'] = $value['total'];
                }else{
                    $location[$value['id_campaign']]['total'] += $value['total'];
                }
            }
        }

        $dados = array();
        foreach ($listCustom['data'] as $row) {
            $dados[$row['campaign']['id']]['id_client'] = $row['id_client'];
            $dados[$row['campaign']['id']]['id_campaign'] = $row['campaign']['id'];
            $dados[$row['campaign']['id']]['name_client'] = $row['name_client'];
            $dados[$row['campaign']['id']]['name_campaign'] = $row['campaign']['name'];
            $dados[$row['campaign']['id']]['base'] = $row['base'];
            $dados[$row['campaign']['id']]['sended'] = $row['sended'];
            $dados[$row['campaign']['id']]['opening'] = $row['opening'];
            $dados[$row['campaign']['id']]['imported'] = $row['imported'];
            $dados[$row['campaign']['id']]['failed'] = $row['failed'];
            $dados[$row['campaign']['id']]['reply'] = $row['reply'];
            $dados[$row['campaign']['id']]['sended_at'] = ($row['sended_at'] != 0)?$row['sended_at']:Null;
        }

        foreach ($location as $key => $value) {
            $dados[$key]['id_client'] = $value['id_client'];
            $dados[$key]['id_campaign'] = $key;
            $dados[$key]['location'] = $value['location'];
            $dados[$key]['total'] = $value['total'];
        }

        foreach ($lastDays['data'] as $row) {
            $dados[$row['id_campaign']]['id_client'] = $row['id_client'];
            $dados[$row['id_campaign']]['id_campaign'] = $row['id_campaign'];
            if(!isset($dados[$row['id_campaign']]['last_days'])){
                $dados[$row['id_campaign']]['last_days'] = $row['date_received']."_".$row['total']."|";
            }else{
                $dados[$row['id_campaign']]['last_days'] .= $row['date_received']."_".$row['total']."|";
            }
        }

        ksort($dados);

        try {
            $hist = new HistListCustom();
            $hist->truncate();
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }

        try {
            foreach ($dados as $row) {
                $hist = new HistListCustom();
                $hist->id_client        = $row['id_client'];
                $hist->id_campaign      = $row['id_campaign'];
                $hist->name_client      = (isset($row['name_client']) != '')?$row['name_client']:Null;
                $hist->name_campaign    = (isset($row['name_campaign']) != '')?$row['name_campaign']:Null;
                $hist->base             = (isset($row['base']) != '')?$row['base']:Null;
                $hist->sended           = (isset($row['sended']) != '')?$row['sended']:Null;
                $hist->opening          = (isset($row['opening']) != '')?$row['opening']:Null;
                $hist->imported         = (isset($row['imported']) != '')?$row['imported']:Null;
                $hist->failed           = (isset($row['failed']) != '')?$row['failed']:Null;
                $hist->reply            = (isset($row['reply']) != '')?$row['reply']:Null;
                $hist->sended_at        = (isset($row['sended_at']) != '')?$row['sended_at']:Null;
                $hist->location         = (isset($row['location']) != '')?$row['location']:Null;
                $hist->total            = (isset($row['total']) != '')?$row['total']:Null;
                $hist->last_days        = (isset($row['last_days']) != '')?$row['last_days']:Null;
                $hist->save();
            }
            return response()->json(LibraryController::responseApi('ok'));
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

    public static function openandreplysended($request, $id = null)
    {
        try {
            $libraryController = new LibraryController;
            if (isset($request->date_init)) {
                if (!$libraryController->validateDate($request->date_init)) {
                    return response()->json(LibraryController::responseApi([], 'date invalid', 100));
                }
            }else {
                $request->merge([
                    'date_init' => Carbon::now()->format('Y-m-d')
                ]);
            }
            if (isset($request->date_end)) {
                if (!$libraryController->validateDate($request->date_end)) {
                    return response()->json(LibraryController::responseApi([], 'date invalid', 100));
                }
            }else {
                $request->merge([
                    'date_end' => Carbon::now()->format('Y-m-d')
                ]);
            }
            $data1 = Carbon::createFromDate($request->date_init);
            $data2 = Carbon::createFromDate($request->date_end);
            if ($data2->lt($data1)) {
                return response()->json(LibraryController::responseApi([], 'date invalid', 100));
            }

            $date_init = new DateTime($request->date_init);
            $date_end = new DateTime($request->date_end);

            $dateRange = array();
            while($date_init <= $date_end){
                $dateRange[] = $date_init->format('Ymd');
                $date_init = $date_init->modify('+1day');
            }

            $resultClients = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    $clients = new Clients;
                    if ($id != null) {
                        $clients = $clients->where('clients.id', $id);
                    }
                    $clients = $clients->where(function($query) use ($request) {
                        $query->whereBetween('list_custom.created_at', [($request->date_init . ' 00:00:00'), ($request->date_end . ' 23:59:59')]);
                    });
                    $resultClients[] = $clients->leftJoin($nameTable.' AS list_custom', 'list_custom.id_client', 'clients.id')
                                        ->leftJoin('log_link_sms', 'list_custom.id', 'log_link_sms.id_list_custom')
                                        ->leftJoin('reply_sms', 'list_custom.id', 'reply_sms.id_list_custom')
                                        ->select('clients.id', DB::raw('count(list_custom.id) sended'), DB::raw('SUM(
                                                                                                            CASE
                                                                                                                WHEN list_custom.id_status_link = 2
                                                                                                                    THEN 1
                                                                                                                WHEN list_custom.id_status_link <> 2
                                                                                                                    THEN 0
                                                                                                            END
                                                                                                        ) opening'), DB::raw('COUNT(reply_sms.id) reply'))
                                        ->groupBy('clients.id')
                                        ->get()->toArray();

                }
            }

            $return = array();
            foreach ($resultClients as $key => $value) {
                foreach ($value as $key => $row) {
                    $return[] = $row;
                }
            }

            return response()->json(LibraryController::responseApi($return));
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

    public static function statuscustom($request)
    {
        try {
            $libraryController = new LibraryController;
            if (isset($request->date_init)) {
                if (!$libraryController->validateDate($request->date_init)) {
                    return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                }
            }else {
                $request->merge([
                    'date_init' => Carbon::now()->format('Y-m-d')
                ]);
            }
            if (isset($request->date_end)) {
                if (!$libraryController->validateDate($request->date_end)) {
                    return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                }
            }else {
                $request->merge([
                    'date_end' => Carbon::now()->format('Y-m-d')
                ]);
            }

            $data1 = Carbon::createFromDate($request->date_init);
            $data2 = Carbon::createFromDate($request->date_end);
            if ($data2->lt($data1)) {
                return response()->json(LibraryController::responseApi([], 'invalid date', 100));
            }

            $init_date = new DateTime($request->date_init);
            $end_date = new DateTime($request->date_end);

            $dateRange = array();
            while($init_date <= $end_date){
                $dateRange[] = $init_date->format('Ymd');
                $init_date = $init_date->modify('+1day');
            }

            $nameTable = "";
            $statusListCustom = [];
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    if (isset($request->id_campaign) && $request->id_campaign) {
                        $campaigns = Campaigns::where('campaigns.id', $request->id_campaign);
                    }else {
                        $campaigns = new Campaigns();
                    }
                    $campaigns = $campaigns->join('clients', 'campaigns.id_client', '=', 'clients.id');
                    $campaigns = $campaigns->where(function($query) use ($request, $nameTable) {
                        $query->whereNotNull(DB::raw("(SELECT id FROM $nameTable AS list_custom WHERE list_custom.id_campaign = campaigns.id and list_custom.created_at Between '$request->date_init 00:00:00' AND '$request->date_end  23:59:59'  limit 1)"));
                        $query->orWhereNotNull(DB::raw("(SELECT id FROM log_import_errors WHERE log_import_errors.id_campaigns = campaigns.id and log_import_errors.created_at Between '$request->date_init 00:00:00' AND '$request->date_end 23:59:59' limit 1)"));
                    });
                    $campaigns = $campaigns->select('campaigns.*', DB::raw('clients.name as name_client'))->distinct()->get();

                    foreach ($campaigns as $key => $value) {

                        $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom")
                                                    ->where('id_campaign', $value->id)
                                                    ->whereBetween('created_at', [($request->date_init . ' 00:00:00'), ($request->date_end . ' 23:59:59')])
                                                    ->select('id_client', 'id_campaign', DB::raw('max(sended_at) as sended_at'), DB::raw('COUNT(id) imported, SUM(CASE WHEN list_custom.id_send_sms = 1 THEN 1 WHEN list_custom.id_send_sms <> 1 THEN 0 END) sended'), DB::raw('SUM(CASE WHEN list_custom.id_status_link = 2 THEN 1 WHEN list_custom.id_status_link <> 2 THEN 0 END) opening'))
                                                    ->groupBy('id_client', 'id_campaign')
                                                    ->first();

                        $logImportError = LogImportError::where('id_campaigns', $value->id)
                                                            ->whereBetween('created_at', [($request->date_init . ' 00:00:00'), ($request->date_end . ' 23:59:59')])
                                                            ->select(DB::raw('COUNT(*) failed'))->first();

                        $replySms = ReplySms::join($nameTable.' AS list_custom', 'list_custom.id', '=', 'reply_sms.id_list_custom')
                                            ->where('list_custom.id_campaign', $value->id)
                                            ->whereBetween('reply_sms.created_at', [($request->date_init . ' 00:00:00'), ($request->date_end . ' 23:59:59')])
                                            ->select('list_custom.id_campaign', DB::raw('COUNT(DISTINCT reply_sms.id) as reply'))
                                            ->groupBy('list_custom.id_campaign')
                                            ->first();

                        $statusListCustom[] = [
                            'id_client' => $value->id_client,
                            'name_client' => $value->name_client,
                            'base' => ((isset($listCustom->imported) ? $listCustom->imported : 0) + (isset($logImportError['failed']) ? $logImportError['failed']: 0)),
                            'imported' => (isset($listCustom->imported) ? $listCustom->imported : 0),
                            'failed' => (isset($logImportError->failed) ? $logImportError->failed: 0),
                            'sended' => (int)(isset($listCustom->sended) ? $listCustom->sended : 0),
                            'opening' => (int)(isset($listCustom->opening) ? $listCustom->opening : 0),
                            'reply' => (isset($replySms['reply']) ? $replySms['reply'] : 0),
                            'sended_at' => (isset($listCustom->sended_at) ? $listCustom->sended_at : 0),
                            'campaign' => [
                                'id'=> $value->id,
                                'name'=> $value->name
                            ]
                        ];
                    }
                }
            }

            return response()->json(LibraryController::responseApi($statusListCustom));
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

    public static function lastdays($request)
    {
        try {

            $dataAtual = Carbon::now()->format('Y-m-d');
            $data7Dias = Carbon::now()->subDays(6)->format('Y-m-d');

            $dataAtual = new DateTime($dataAtual);
            $data7Dias = new DateTime($data7Dias);

            $dateRange = array();
            while($data7Dias <= $dataAtual){
                $dateRange[] = $data7Dias->format('Ymd');
                $data7Dias = $data7Dias->modify('+1day');
            }

            $data7Dias = Carbon::now()->subDays(6)->format('Y-m-d');
            $dataAtual = Carbon::now()->format('Y-m-d');

            $nameTable = "";
            $resultReplySms = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    $data = Carbon::parse($row)->format('Y-m-d');

                    $replySms = ReplySms::join($nameTable.' AS list_custom', 'list_custom.id', 'reply_sms.id_list_custom')
                                        ->select('id_client', 'id_campaign', DB::raw("DATE(received_at) date_received"), DB::raw('count(DISTINCT reply_sms.id) total'));

                    $replySms = $replySms->whereBetween('received_at', [($data . ' 00:00:00'), ($data . ' 23:59:59')]);

                    $replySms = $replySms->groupBy('id_client');
                    $replySms = $replySms->groupBy('id_campaign');
                    $replySms = $replySms->groupBy('date_received');
                    $replySms = $replySms->orderBY('date_received');
                    $resultReplySms[] = $replySms->get()->toArray();

                }
            }

            $return = array();
            foreach ($resultReplySms as $key => $value) {
                foreach ($value as $row) {
                    $return[] = $row;
                }
            }

            return response()->json(LibraryController::responseApi($return));
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

    public static function greateropening($request)
    {
        try {
            $date_init = new DateTime($request->date_init);
            $date_end = new DateTime($request->date_end);

            $dateRange = array();
            while($date_init <= $date_end){
                $dateRange[] = $date_init->format('Ymd');
                $date_init = $date_init->modify('+1day');
            }

            $responselistCustom = array();
            $responseListCustomTotal = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    $data = Carbon::parse($row)->format('Y-m-d');

                    $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom")->where('list_custom.id_send_sms', 1);

                    $listCustom = $listCustom->select('id_client', 'id_campaign', DB::raw("case
                                                WHEN TRIM(list_custom.ddd) in(68) THEN 'AC'
                                                WHEN TRIM(list_custom.ddd) in(82) THEN 'AL'
                                                WHEN TRIM(list_custom.ddd) in(97,92) THEN 'AM'
                                                WHEN TRIM(list_custom.ddd) in(96) THEN 'AP'
                                                WHEN TRIM(list_custom.ddd) in(77,75,73,74,71) THEN 'BA'
                                                WHEN TRIM(list_custom.ddd) in(88,85) THEN 'CE'
                                                WHEN TRIM(list_custom.ddd) in(61) THEN 'DF'
                                                WHEN TRIM(list_custom.ddd) in(28,27) THEN 'ES'
                                                WHEN TRIM(list_custom.ddd) in(62,64,61) THEN 'GO'
                                                WHEN TRIM(list_custom.ddd) in(99,98) THEN 'MA'
                                                WHEN TRIM(list_custom.ddd) in(34,37,31,33,35,38,32) THEN 'MG'
                                                WHEN TRIM(list_custom.ddd) in(67) THEN 'MS'
                                                WHEN TRIM(list_custom.ddd) in(65,66) THEN 'MT'
                                                WHEN TRIM(list_custom.ddd) in(91,94,93) THEN 'PA'
                                                WHEN TRIM(list_custom.ddd) in(83) THEN 'PB'
                                                WHEN TRIM(list_custom.ddd) in(81,87) THEN 'PE'
                                                WHEN TRIM(list_custom.ddd) in(89,86) THEN 'PI'
                                                WHEN TRIM(list_custom.ddd) in(43,41,42,44,45,46) THEN 'PR'
                                                WHEN TRIM(list_custom.ddd) in(24,22,21) THEN 'RJ'
                                                WHEN TRIM(list_custom.ddd) in(84) THEN 'RN'
                                                WHEN TRIM(list_custom.ddd) in(69) THEN 'RO'
                                                WHEN TRIM(list_custom.ddd) in(95) THEN 'RR'
                                                WHEN TRIM(list_custom.ddd) in(53,54,55,51) THEN 'RS'
                                                WHEN TRIM(list_custom.ddd) in(47,48,49) THEN 'SC'
                                                WHEN TRIM(list_custom.ddd) in(79) THEN 'SE'
                                                WHEN TRIM(list_custom.ddd) in(11,12,13,14,15,16,17,18,19) THEN 'SP'
                                                WHEN TRIM(list_custom.ddd) in(63) THEN 'TO'
                                                end estado"),
                                    DB::raw("count(*)total"))
                                ->groupBy('id_client')
                                ->groupBy('id_campaign')
                                ->groupBy('estado')
                                ->orderBy('total', 'desc');

                    $listCustom = $listCustom->whereBetween('list_custom.sended_at', [($data . ' 00:00:00'), ($data . ' 23:59:59')]);

                    $responselistCustom[] = $listCustom->get()->toArray();

                }
            }

            $somaTotal = array();
            foreach ($responselistCustom as $key => $value) {
                foreach ($value as $row) {
                    $somaTotal[$row->id_client][$row->estado]['total'] = $row->total;
                }
            }

            $response = array();
            foreach ($responselistCustom as $key => $value) {
                if ($value) {
                    foreach ($value as $row) {
                        $response[$row->id_client][$row->estado]['id_client'] = $row->id_client;
                        $response[$row->id_client][$row->estado]['id_campaign'] = $row->id_campaign;
                        $response[$row->id_client][$row->estado]['estado'] = $row->estado;
                        $response[$row->id_client][$row->estado]['total'] = $somaTotal[$row->id_client][$row->estado]['total'];
                    }
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

}
