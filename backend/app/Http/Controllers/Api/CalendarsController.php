<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListHash;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\LibraryController;
use App\Models\LogLinkSms;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
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
            ListHash::where('hash', $hash)->update(['id_status' => 1]);

            $listHash = ListHash::where('hash', $hash)->first();

            $library = new LibraryController();
            if (!$listHash) {
                return $library->responseApi([],'Ops! Este link expirou',404);
            }
            $arrayListHash = $listHash->toArray();

            $tableListCustom = 'list_custom_'.Carbon::parse($arrayListHash['created_at'])->format('Ymd');

            $listcustom = DB::connection('mysql2')->table($tableListCustom)
                                    ->where('id', $arrayListHash['id_list_custom'])
                                    ->get()
                                    ->toArray();


            $arrayListCustom = get_object_vars($listcustom[0]);

            $link_opened = LogLinkSms::where('id_list_custom', $arrayListCustom['id'])->get()->count();
            if ($request->device_type) {
                $device_type = $request->device_type;
            }else {
                $device_type = 'desktop';
            }

            if ($link_opened == 0) {
                DB::connection('mysql2')->table("list_custom_".Carbon::parse($arrayListHash['created_at'])->format('Ymd'))
                                ->where('id', $arrayListCustom['id'])
                                ->update([
                                    'id_status_link' => 2,
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                $logLinkSms = new LogLinkSms;
                $logLinkSms->id_list_custom = $arrayListCustom['id'];
                $logLinkSms->date_opened = now();
                $logLinkSms->device_type = $device_type;
                $logLinkSms->save();
            }

            return $library->responseApi($arrayListCustom);
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
