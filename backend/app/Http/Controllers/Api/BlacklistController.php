<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\Blacklist;
use App\Models\UserClient;
use Carbon\Carbon;
use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlacklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        try {
            $user = auth('api')->user();
            $blacklist = new Blacklist();

            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $blacklist = $blacklist->whereIn('id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $blacklist = $blacklist->where('id_client', $user->id_client);
                }
            }

            return response()->json(LibraryController::responseApi($blacklist->get()));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            } else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([], $e->getMessage(), $code));
        }
    }

    public function advancedSearch(Request $request)
    {
        try {
            $check_filtrar = $request->check_filtrar;
            $ddd = $request->ddd;
            $phone = $request->phone;
            $user = auth('api')->user();
            $blacklist = new Blacklist();

            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $blacklist = $blacklist->whereIn('id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $blacklist = $blacklist->where('id_client', $user->id_client);
                }
            }

            if ($check_filtrar == "true") {
                $blacklist = $blacklist->where('ddd', $ddd);
                $blacklist = $blacklist->where('phone', $phone);
            }else{
                $blacklist = $blacklist->select('mailing_file_original', "created_at");
                $blacklist = $blacklist->groupBy('mailing_file_original');
                $blacklist = $blacklist->groupBy('created_at');
            }

            $response = $blacklist->get();

            if ($check_filtrar == "true") {
                $count=0;
                foreach ($response as $key => $value) {
                    $count++;
                    $response[$key]['id'] = $value->id;
                    $response[$key]['mailing_file_original'] = $value->mailing_file_original;
                    $response[$key]['mailing_file_genion'] = $value->mailing_file_genion;
                    $response[$key]['id_client'] = $value->id_client;
                    $response[$key]['phone'] = "(".$value->ddd.")".substr($value->phone, 0, 5)."-".substr($value->phone, 5);
                    $response[$key]['created_at'] = Carbon::parse($value->created_at)->addHours("-3")->format("Y-m-d H:i:s");
                }
            }else{
                $count=0;
                foreach ($response as $key => $value) {
                    $count++;
                    $response[$key]['id'] = $count;
                    $response[$key]['mailing_file_original'] = $value->mailing_file_original;
                    $response[$key]['created_at'] = Carbon::parse($value->created_at)->addHours("-3")->format("Y-m-d H:i:s");
                }
            }

            return response()->json(LibraryController::responseApi($response));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            } else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([], $e->getMessage(), $code));
        }
    }

    public function destroy($id)
    {
        try {
            $blacklist = Blacklist::findOrFail($id);
            $blacklist->delete();
            return response()->json(LibraryController::responseApi($blacklist, 'ok'));
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

    public function destroyLote(Request $request)
    {
        try {
            $mailing_file_original = $request->mailing_file_original;
            $created_at = $request->created_at;

            $blacklist = Blacklist::where("mailing_file_original", $mailing_file_original);
            $blacklist = $blacklist->where("created_at", Carbon::parse($created_at)->format("Y-m-d H:i:s"));
            $blacklist->delete();
            return response()->json(LibraryController::responseApi($blacklist, 'ok'));
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

    public function upload(Request $request)
    {
        $file_name_original = $request->file_name_original;
        $file_name_genion = $request->file_name_genion;
        $id_client = $request->id_client;

        LoadFile::connection("mysql2")
            ->file(base_path() ."/../frontend/storage/app/upload/". $file_name_genion, $local = true)
            ->into("blacklist")
            ->columns(['phone'])
            ->fieldsTerminatedBy(';')
            ->linesTerminatedBy('\n')
            ->ignoreLines(1)
            ->set([
                'mailing_file_original' => $file_name_original,
                'mailing_file_genion' => $file_name_genion,
                'created_at' => DB::raw('NOW()'),
                'id_client' => $id_client,
                'ddd' => DB::raw("substring( replace(replace(phone,'.',''),'-','') ,1,2)"),
                'phone' => DB::raw("TRIM(REPLACE(REPLACE(substring( replace(replace(phone,'.',''),'-','')  ,3,10) , CHAR(13), ''), CHAR(10),''))")
            ])
            ->load();

        return response()->json(LibraryController::responseApi("", 'ok'));
    }

    public function downloadFile(Request $request)
    {
        try {

            $mailing_file_original = $request->mailing_file_original;
            $created_at = $request->created_at;
            $user = auth('api')->user();

            $blacklist = new Blacklist();

            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $blacklist = $blacklist->whereIn('id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $blacklist = $blacklist->where('id_client', $user->id_client);
                }
            }

            $blacklist = $blacklist->where('mailing_file_original', $mailing_file_original);
            $blacklist = $blacklist->where(DB::raw('DATE(created_at)'), Carbon::parse($created_at)->format("Y-m-d"));

            $resultBlacklist = $blacklist->get()->toArray();

            return LibraryController::responseApi($resultBlacklist);
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
