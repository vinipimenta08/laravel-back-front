<?php

namespace App\Jobs;

use App\Http\Controllers\Api\smsController;
use App\Http\Controllers\LibraryController;
use App\Models\Clients;
use App\Models\ListCustom;
use App\Models\LogImport;
use App\Models\StartStopSms;
use App\Models\BatchSendControl;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $request = null;
    private $interval = 0;
    private $user = null;
    public $timeout = 3600;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $interval, $user)
    {
        $this->request = $request;
        $this->interval = $interval;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $hashQueue = hash("crc32",$this->request['id_campaign']);
            $nameTable = $this->request['nameTable'];

            DB::connection('mysql2')->table($nameTable)
                    ->where('id_send_sms', $this->request['id_send_sms'])
                    ->where('id_campaign', $this->request['id_campaign'])
                    ->where('id_client', $this->request['id_client'])
                    ->update(['id_send_sms' => 4]);

            $listCustom = $this->request['listCustom'];

            LogImport::create([
                'id_user' => $this->user->id,
                'id_client' => $this->request['id_client'],
                'id_campaign' => $this->request['id_campaign'],
                'qtd_import' => count($listCustom),
                'send_sms' => $this->request['id_send_sms']
            ]);

            $smsController = new smsController;

            $validClientJustSMS =  $this->validClientJustSMS($this->request['id_client']);

            // ZENVIA AND KOLMEYA
            if(!$validClientJustSMS){

                foreach ($listCustom as $key => $row) {
                    foreach ($row as $value) {

                        $startStopSms = StartStopSms::where('hash_id_campaign', $hashQueue)->first();
                        if ($startStopSms->run == 0) {
                            break;
                        }

                        $smszenvia = [];
                        $smskolmeya = [];
                        $retornoSendSms = [];
                        $total = $value;
                        $to = [
                                ["id" => $value->id,
                                "phone" => $value->ddd . $value->phone,
                                "message" => $value->message_sms . ' ' . env('URL_SHORTENER_'.env('ENVIRONMENT')) . $value->hash
                                ]
                        ];
                        $smskolmeya = $smsController->sendsmskolmeya($to);
                        if(isset($smskolmeya['valids']))
                        foreach ($smskolmeya['valids'] as $key => $valueresponse) {
                            DB::connection('mysql2')->table($nameTable)
                                        ->where('id', $value->id)
                                        ->update([
                                            'sended_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                            'id_send_sms' => 1,
                                            'id_sms' => $valueresponse['id'],
                                            'attempt' => $value->attempt + 1,
                                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                        ]);
                        }
                        $to = [];
                        if(isset($smskolmeya['invalids']))
                        foreach ($smskolmeya['invalids'] as $key => $valueresponse) {
                            $listCustomInvalids = DB::connection('mysql2')->table($nameTable)
                                            ->where('id', $value->id)
                                            ->first();
                            $to[] = [
                                "id"=> ($listCustomInvalids['id']),
                                "phone"=> $listCustomInvalids['phone'],
                                "message" => $listCustomInvalids['message_sms']
                            ];
                        }
                        if(isset($smskolmeya['messages']) || (isset($smskolmeya[0]) ? ($smskolmeya[0] == "Saldo Insuficiente") : false) || (isset($smskolmeya['erro']) ? ($smskolmeya['erro'] == "temporariamente indisponivel") : false)){
                            $to =
                                ["id" => $value->id,
                                "phone" => $value->ddd . $value->phone,
                                "message" => $value->message_sms . ' ' . env('URL_SHORTENER_'.env('ENVIRONMENT')) . $value->hash
                                ];
                        }
                        if(count($to)){
                            $smszenvia = $smsController->sendsmszenvia($to);
                            if ($smszenvia['sendSmsResponse']['statusCode'] == 00) {
                                DB::connection('mysql2')->table($nameTable)
                                        ->where('id', $value->id)
                                        ->update([
                                            'sended_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                            'id_send_sms' => 1,
                                            'id_sms' => $value->id . '_' . $value->phone,
                                            'attempt' => $value->attempt + 1,
                                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                        ]);
                            }else {
                                DB::connection('mysql2')->table($nameTable)
                                        ->where('id', $value->id)
                                        ->update([
                                            'id_send_sms' => 2,
                                            'attempt' => $value->attempt + 1,
                                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                        ]);
                            }
                        }
                        $retornoSendSms = ['smskolmeya' => $smskolmeya, 'smszenvia' => $smszenvia];
                        usleep($this->interval);

                    }

                }

            }

            // TALKIP
            if($validClientJustSMS){

                $to = [];
                foreach ($listCustom as $key => $row) {
                    foreach ($row as $value) {
                        $startStopSms = StartStopSms::where('hash_id_campaign', $hashQueue)->first();
                        if ($startStopSms->run == 0) {
                            break;
                        }

                        DB::connection('mysql2')->table($nameTable)
                                        ->where('id', $value->id)
                                        ->update([
                                            'attempt' => $value->attempt + 1
                                        ]);

                        $to[] = [
                            "id"=> ($value->id),
                            "reference"=> ($value->id),
                            "phone"=> $value->ddd . $value->phone,
                            "message" => $value->message_sms
                        ];
                    }
                }

                if (count($to) > 0) {

                    try {
                        // MULTIPLE
                        $reponserTalkip = $smsController->sendsmstalkip($to, 'multiple');

                        $id = $reponserTalkip['id'] . "_talkip";

                        foreach ($to as $key => $value) {
                            DB::connection('mysql2')->table($nameTable)
                                    ->where('id', $value['id'])
                                    ->update([
                                        'id_send_sms' => 5,
                                        'id_sms' => $id,
                                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                    ]);

                        }

                        $batch = new BatchSendControl();
                        $batch->id_sms = $id;
                        $batch->search_date = Carbon::now()->format('Y-m-d H:i:s');
                        $batch->save();

                    } catch (Exception $e) {
                        LibraryController::recordError($e);
                        foreach ($to as $key => $value) {
                            DB::connection('mysql2')->table($nameTable)
                                    ->where('id', $value['id'])
                                    ->update([
                                        'id_send_sms' => 2,
                                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                    ]);
                        }
                    }

                }

            }

            $startStopSms = StartStopSms::where('hash_id_campaign',$hashQueue)->first();
            $startStopSms->run = 0;
            $startStopSms->save();
        } catch (Exception $e) {
            LibraryController::recordError($e);
        }
    }

    public static function validClientJustSMS($id_client)
    {
        try{
            $clients = new Clients();
            $clients = $clients->where('id', $id_client);
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
}
