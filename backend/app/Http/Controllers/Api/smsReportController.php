<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Jobs\QueueJobs;
use App\Jobs\ResendSmsJob;
use App\Models\Clients;
use App\Models\ListCustom;
use App\Models\ReplySms;
use App\Models\StartStopSms;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class smsReportController extends Controller
{

    function responseKolmeyaSms(Request $request)
    {
        try {
            $reply = ReplySms::where('id_reference', $request->id)->orderBy('received_at', 'asc')->get();
            return response()->json(LibraryController::responseApi($reply));
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

    public function sendbystatusendcampaign(Request $request)
    {
        try {
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
            $hashQueue = hash("crc32",$request->id_campaign);
            StartStopSms::updateOrCreate(
                ['hash_id_campaign' => $hashQueue],
                ['hash_id_campaign' => $hashQueue, 'run' => $request->run]
                );
            if ($request->run) {
                if (!isset($request->interval)) {
                    $interval = 0;
                }else {
                    $interval = $request->interval;
                }

                $init_date = new DateTime($request->init_date);
                $end_date = new DateTime($request->end_date);

                $dateRange = array();
                while($init_date <= $end_date){
                    $dateRange[] = $init_date->format('Ymd');
                    $init_date = $init_date->modify('+1day');
                }

                $listcustom = array();
                $nameTableArray = array();
                foreach ($dateRange as $row) {
                    $nameTable = "list_custom_".$row;

                    $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                    if ($query) {
                        $listcustom[] = DB::connection('mysql2')->table($nameTable)
                                    ->where('id_send_sms', $request->id_send_sms)
                                    ->where('id_campaign', $request->id_campaign)
                                    ->where('id_client', $request->id_client)
                                    ->get()
                                    ->toArray();

                        $chunk = array_chunk($listcustom, env('LIMIT_QUEUE_SMS'));

                        foreach ($chunk as $key => $listCustom) {
                            $request->request->add(['nameTable' => $nameTable]);
                            $request->request->add(['listCustom' => $listCustom]);
                            ResendSmsJob::dispatch($request->all(), $interval, auth('api')->user())->onQueue($hashQueue);
                            QueueJobs::dispatch($hashQueue);
                        }
                    }
                }

            }
            return response()->json(LibraryController::responseApi([], 'ok'));
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

    public function openandreplysended(Request $request, $id = null)
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
            $user = auth('api')->user();

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
                    if ($user->id_profile != 1) {
                        $clients = $clients->where('clients.id', $user->id_client);
                    }else {
                        if ($id != null) {
                            $clients = $clients->where('clients.id', $id);
                        }
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

    public function lastdays(Request $request)
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

                    $user = auth('api')->user();
                    $replySms = ReplySms::join($nameTable.' AS list_custom', 'list_custom.id', 'reply_sms.id_list_custom')
                                        ->select(DB::raw("DATE(received_at) date_received"), DB::raw('count(DISTINCT reply_sms.id) total'));
                    if ($user->id_profile != 1) {
                        $replySms = $replySms->where('list_custom.id_client', $user->id_client);
                    }else {
                        if (isset($request->id_client)) {
                            $replySms = $replySms->where('list_custom.id_client', $request->id_client);
                        }
                    }

                    $replySms = $replySms->whereBetween('received_at', [($data . ' 00:00:00'), ($data . ' 23:59:59')]);

                    $replySms = $replySms->groupBy('date_received');
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
}
