<?php

namespace App\Jobs;

use App\Http\Controllers\Api\smsController;
use App\Http\Controllers\LibraryController;
use App\Models\BrokerSms;
use App\Models\Clients;
use App\Models\ListCustom;
use App\Models\MailingProcess;
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
use Illuminate\Support\Str;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $request = null;
    private $userLog = null;
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
        $this->userLog = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $id_client = $this->request['id_client'];
            $id_campaign = $this->request['id_campaign'];
            $nameTable = $this->request['nameTable'];
            $mailing_file_original = $this->request['mailing_file_original'];
            $mailing_file_genion = $this->request['mailing_file_genion'];
            $id_send_sms = $this->request['id_send_sms'];
            $ids = $this->request['ids'];
            $Clients = Clients::where('id', $id_client )->get();
            $to = $this->request['custom'];

            foreach ($to as $key => $value) {
                $idsLote[] = $value['id'];
            }

            $broker_client = $Clients[0]->broker_sms;
            $smsController = new smsController;
            if($broker_client == "1,2"){
                $smszenvia = [];
                $smskolmeya = [];
                if (count($to)) {
                    $total = $to;

                    foreach ($to as $key => $value) {
                        try {
                            $responseZenvia = $smsController->sendsmszenvia($value);
                            $smszenvia[] = $responseZenvia;
                            if ($responseZenvia['sendSmsResponse']['statusCode'] == 00) {
                                DB::connection('mysql2')->table($nameTable)
                                                ->where('id', $value['id'])
                                                ->update([
                                                    'sended_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'id_send_sms' => 1,
                                                    'id_sms' => $value['id'] . '_' . $value['phone'],
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                            }else {
                                DB::connection('mysql2')->table($nameTable)
                                                ->where('id', $value['id'])
                                                ->update([
                                                    'id_send_sms' => 2,
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                            }
                        } catch (Exception $e) {
                            DB::connection('mysql2')->table($nameTable)
                                                ->where('id', $value['id'])
                                                ->update([
                                                    'id_send_sms' => 2,
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                        }
                    }

                    $smskolmeya = $smsController->sendsmskolmeya($to);
                    if(isset($smskolmeya['valids']))
                    foreach ($smskolmeya['valids'] as $key => $value) {
                        DB::connection('mysql2')->table($nameTable)
                                                ->where('phone', $value['phone'])
                                                ->where('id', $value['reference'])
                                                ->whereIn('id', $ids)
                                                ->update([
                                                    'sended_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'id_send_sms' => 1,
                                                    'id_sms' => $value['id'],
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                    }
                    $to = [];
                    if(isset($smskolmeya['invalids']))
                    foreach ($smskolmeya['invalids'] as $key => $value) {
                        $listCustomInvalids = DB::connection('mysql2')->table($nameTable)
                                    ->where('phone', $value['phone'])
                                    ->whereIn('id', $ids)
                                    ->first();
                        $to[] = [
                            "id"=> ($listCustomInvalids['id']),
                            "phone"=> $listCustomInvalids['phone'],
                            "message" => $listCustomInvalids['message_sms']
                        ];
                    }
                    if(isset($smskolmeya['messages']) || (isset($smskolmeya[0]) ? ($smskolmeya[0] == "Saldo Insuficiente") : false) || (isset($smskolmeya['erro']) ? ($smskolmeya['erro'] == "temporariamente indisponivel") : false)){
                        $to = $total;
                    }

                    $retornoSendSms = ['smskolmeya' => $smskolmeya, 'smszenvia' => $smszenvia];
                    return $retornoSendSms;

                }

            }else if($broker_client == "1"){

                $smszenvia = [];
                if(count($to)){
                    foreach ($to as $key => $value) {
                        try {
                            $responseZenvia = $smsController->sendsmszenvia($value);
                            $smszenvia[] = $responseZenvia;
                            if ($responseZenvia['sendSmsResponse']['statusCode'] == 00) {
                                DB::connection('mysql2')->table($nameTable)
                                                ->where('id', $value['id'])
                                                ->update([
                                                    'sended_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'id_send_sms' => 1,
                                                    'id_sms' => $value['id'] . '_' . $value['phone'],
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                            }else {
                                DB::connection('mysql2')->table($nameTable)
                                                ->where('id', $value['id'])
                                                ->update([
                                                    'id_send_sms' => 2,
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                            }
                        } catch (Exception $e) {
                            DB::connection('mysql2')->table($nameTable)
                                                ->where('id', $value['id'])
                                                ->update([
                                                    'id_send_sms' => 2,
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                        }
                    }
                }

                $retornoSendSms = ['smszenvia' => $smszenvia];
                return $retornoSendSms;
            }else if($broker_client == "2"){
                $smskolmeya = [];
                if (count($to)) {
                    $total = $to;

                    $smskolmeya = $smsController->sendsmskolmeya($to);
                    if(isset($smskolmeya['valids']))
                    foreach ($smskolmeya['valids'] as $key => $value) {
                        DB::connection('mysql2')->table($nameTable)
                                                ->where('phone', $value['phone'])
                                                ->where('id', $value['reference'])
                                                ->whereIn('id', $ids)
                                                ->update([
                                                    'sended_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'id_send_sms' => 1,
                                                    'id_sms' => $value['id'],
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                    }
                    $to = [];
                    if(isset($smskolmeya['invalids']))
                    foreach ($smskolmeya['invalids'] as $key => $value) {
                        DB::connection('mysql2')->table($nameTable)
                                                ->where('phone', $value['phone'])
                                                ->where('id', $value['reference'])
                                                ->whereIn('id', $ids)
                                                ->update([
                                                    'sended_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                                    'id_send_sms' => 2,
                                                    'attempt' => 1,
                                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                                ]);
                    }

                    $retornoSendSms = ['smskolmeya' => $smskolmeya];
                    return $retornoSendSms;

                }

            }else if($broker_client == "3"){

                $smsbroker = [];
                if (count($to)) {
                    $total = $to;

                    try {
                        // MULTIPLE
                        $reponserTalkip = $smsController->sendsmstalkip($to, 'multiple');

                        $id = $reponserTalkip['id'] . "_talkip";

                        DB::connection('mysql2')->table($nameTable)
                                ->where('mailing_file_original', $mailing_file_original)
                                ->where('mailing_file_genion', $mailing_file_genion)
                                ->where('id_campaign', $id_campaign)
                                ->where('id_client', $id_client)
                                ->where('id_send_sms', $id_send_sms)
                                ->whereIn('id', $idsLote)
                                ->update([
                                    'id_send_sms' => 6,
                                    'id_sms' => $id,
                                    'attempt' => 1,
                                    'sended_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                                ]);

                        BatchSendControl::create([
                            'id_sms' => $id,
                            'search_date' => Carbon::now()->format('Y-m-d H:i:s'),
                            'mailing_file_original' => $mailing_file_original,
                            'mailing_file_genion' => $mailing_file_genion
                        ]);

                    } catch (Exception $e) {
                        LibraryController::recordError($e);
                        DB::connection('mysql2')->table($nameTable)
                                ->where('mailing_file_original', $mailing_file_original)
                                ->where('mailing_file_genion', $mailing_file_genion)
                                ->where('id_campaign', $id_campaign)
                                ->where('id_client', $id_client)
                                ->where('id_send_sms', $id_send_sms)
                                ->whereIn('id', $idsLote)
                                ->update([
                                    'id_send_sms' => 2,
                                    'attempt' => 1
                                ]);
                    }

                }

                $retornoSendSms = ['smsbroker' => $smsbroker];
                return $retornoSendSms;
            }else{

                throw new Exception ('The broker are not valid: '.$broker_client);
            }

        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

}
