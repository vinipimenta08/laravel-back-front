<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use stdClass;

class CheckExistsWhatsappController extends Controller{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function index()
    {

        try {
            $head = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            $nameTable = "list_custom_".Carbon::now()->format('Ymd');
            $listcustom = DB::connection('mysql2')->table($nameTable)
                                ->get()
                                ->toArray();

            $data = [];
            foreach ($listcustom as $key => $value) {
                array_push($data, [
                                'phone'                 => '+55'.$value->ddd.$value->phone,
                                'idGenion'              => $value->id
                            ]);
            }
            $body = [
                'list' => $data,
            ];

            if(count($data) > 0){
                $response = Http::timeout(50)->withHeaders($head)->post('http://18.191.74.190:3001/verifyWhats', $body );
                return $response->json();

            }else{
                $code = 500;
                return response()->json(LibraryController::responseApi([], 'Sem dados', $code));
            }
        } catch (Exception $e) {
            LibraryController::recordError($e);
            $code = 500;
            return response()->json(LibraryController::responseApi([], $e, $code));
        }
    }
}
