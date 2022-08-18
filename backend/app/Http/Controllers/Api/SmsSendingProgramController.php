<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Jobs\ProcessMailingSmsJob;
use App\Jobs\QueueJobs;
use App\Jobs\UpdateSmsProgrammedJob;
use App\Models\Campaigns;
use App\Models\SmsSendingProgram;
use App\Models\UserClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Http\Request;

class SmsSendingProgramController extends Controller
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
            $smsProgram = new SmsSendingProgram;
            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $smsProgram = $smsProgram->whereIn('id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $smsProgram = $smsProgram->where('id_client', $user->id_client);
                }
            }
            $smsProgram = $smsProgram->where('researched', 0);
            $smsProgram = $smsProgram->orderBy('programmed_at')->get()->toArray();

            $campaigns = new Campaigns();
            $campaign = $campaigns->get()->toArray();
            foreach ($smsProgram as $key => $value) {
                foreach ($campaign as $row) {
                    if ($row['id'] == $value['id_campaign']) {
                        $smsProgram[$key]['name_campaign'] = $row['name'];
                    }
                }
            }

            return response()->json(LibraryController::responseApi($smsProgram));


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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        try {
            $rules = [
                "id_campaign" => "required",
                "programmed_at" => "required"
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }

            $campaigns = Campaigns::where('id', $request->id_campaign)->get()->toArray();
            foreach ($campaigns as $row) {
                $id_client = $row['id_client'];
            }
            $request->request->add(['id_client' => $id_client]);

            $smsProgram = new SmsSendingProgram;
            $smsProgram->fill($request->all());
            $smsProgram->save();
            return response()->json(LibraryController::responseApi($smsProgram, "ok"));
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
            $smsProgram = SmsSendingProgram::findOrFail($id);

            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $smsProgram = $smsProgram->whereIn('id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $smsProgram = $smsProgram->where('id_client', $user->id_client);
                }

            }
            $smsProgram = $smsProgram->where('id', $id)->get()->toArray();

            $campaigns = new Campaigns();
            $campaign = $campaigns->get()->toArray();
            foreach ($smsProgram as $key => $value) {
                foreach ($campaign as $row) {
                    if ($row['id'] == $value['id_campaign']) {
                        $smsProgram[$key]['name_campaign'] = $row['name'];
                    }
                }
            }

            return response()->json(LibraryController::responseApi($smsProgram));
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
            $rules = [
                "programmed_at" => "required"
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $user = auth('api')->user();

            if ($user->id_profile == 1) {
                $campaigns = Campaigns::where('id', $request->id_campaign)->get()->toArray();
                foreach ($campaigns as $row) {
                    $id_client = $row['id_client'];
                }
                $request->request->add(['id_client' => $id_client]);
            }

            $smsProgram = new SmsSendingProgram;
            $smsProgram = $smsProgram->findOrFail($id);
            $smsProgram = $smsProgram->fill($request->all());
            LibraryController::logupdate($smsProgram);
            $smsProgram->save();
            return response()->json(LibraryController::responseApi($smsProgram, 'ok'));
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
            $smsProgram = new SmsSendingProgram;
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $smsProgram = $smsProgram->whereIn('id_client', $userClient);
                } else {
                    $smsProgram = $smsProgram->where('id_client', $user->id_client);
                }
            }
            $smsProgram = $smsProgram->findOrFail($id);
            $smsProgram->active = 0;
            $smsProgram->save();
            $smsProgram = $smsProgram->delete();
            return response()->json(LibraryController::responseApi([], 'ok'));
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

    public function active(Request $request)
    {
        try {
            $user = auth('api')->user();
            $smsProgram = new SmsSendingProgram;
            $smsProgram = $smsProgram->findOrFail($request->id);

            if ($smsProgram->active == 1) {
                $smsProgram->active = 0;
            }else{
                $smsProgram->active = 1;
            }
            LibraryController::logupdate($smsProgram);
            $smsProgram->update();
            return response()->json(LibraryController::responseApi([], 'ok'));
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

    public static function sendSmsProgrammed()
    {
        try {
            $data = Carbon::now()->format('Y-m-d H:i:s');
            $smsProgram = new SmsSendingProgram;
            $smsProgram = $smsProgram->where("researched", 0);
            $smsProgram = $smsProgram->where("active", 1);
            $smsProgram = $smsProgram->whereBetween("programmed_at", [Carbon::now()->format('Y-m-d')." 00:00:00", $data]);
            $smsProgram = $smsProgram->get()->toArray();

            foreach ($smsProgram as $row) {
                $queue['nameTable'] = "list_custom_".Carbon::parse($row['created_at'])->format('Ymd');
                $queue['mailing_file_original'] = $row['mailing_file_original'];
                $queue['mailing_file_genion'] = $row['mailing_file_genion'];
                $queue['id_campaign'] = $row['id_campaign'];
                $queue['id_client'] = $row['id_client'];
                $queue['send_sms'] = 1;
                $queue['id_send_sms'] = 17;
                $nameHash = $row['id_campaign']. Carbon::now()->format('YmdHi');
                $hashQueue = hash("crc32",$nameHash);
                ProcessMailingSmsJob::dispatch($queue, 0, auth('api')->user())->onQueue($hashQueue);
                QueueJobs::dispatch($hashQueue);
            }

            $smsProgram = new SmsSendingProgram;
            $smsProgram = $smsProgram->where("researched", 0);
            $smsProgram = $smsProgram->where("active", 1);
            $smsProgram = $smsProgram->whereBetween("programmed_at", [Carbon::now()->format('Y-m-d')." 00:00:00", $data]);
            $smsProgram = $smsProgram->update([
                "researched" => 1
            ]);

            return response()->json(LibraryController::responseApi([], 'ok'));
        } catch (Exception $e) {
            Log::debug('Log: ' . $e);
        }
    }

    public function updateSmsProgrammed(Request $request)
    {
        $mailing_file_genion_atual = $request->mailing_file_genion_atual;
        $mailing_file_genion = $request->mailing_file_genion;
        $id_campaign = $request->id_campaign;
        $created_at = $request->created_at;
        $programmed_at = $request->programmed_at;

        Log::debug('FUNCTION updateSmsProgrammed()');

        try {
            $user = auth('api')->user();

            if ($id_campaign == "") {
                return response()->json(LibraryController::responseApi([],'Campaign not found', 100));
            }

            $campaigns = new Campaigns();
            $campaigns = $campaigns->where("id", $id_campaign);
            $campaign = $campaigns->get()->toArray();

            foreach ($campaign as $key => $value) {
                $id_client = $value['id_client'];
            }

            $queue['mailing_file_genion_atual'] = $mailing_file_genion_atual;
            $queue['mailing_file_genion'] = $mailing_file_genion;
            $queue['id_campaign'] = $id_campaign;
            $queue['id_client'] = $id_client;
            $queue['created_at'] = $created_at;
            $queue['programmed_at'] = $programmed_at;
            $queue['user'] = $user;
            // dd($queue);
            $nameHash = $id_campaign. Carbon::now()->format('YmdHi');
            $hashQueue = hash("crc32",$nameHash);
            UpdateSmsProgrammedJob::dispatch($queue, 0, auth('api')->user())->onQueue($hashQueue);
            QueueJobs::dispatch($hashQueue);

            Log::debug('APÓS UpdateSmsProgrammedJob');

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

}
