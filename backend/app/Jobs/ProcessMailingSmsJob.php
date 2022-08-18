<?php

namespace App\Jobs;

use App\Http\Controllers\LibraryController;
use App\Models\Campaigns;
use App\Models\Clients;
use App\Models\ListCustom;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMailingSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $request  = null;
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
            $user =  $this->userLog;
            $request = $this->request;
            $shippingBatch = [];
            $to = [];
            $ids = [];
            $validClientJustSMS = $this->validClientJustSMS($request['id_client']);

            $id_campaign = $request['id_campaign'];
            $id_client = $request['id_client'];
            $nameTable = $request['nameTable'];
            $mailing_file_original = $request['mailing_file_original'];
            $mailing_file_genion = $request['mailing_file_genion'];
            $id_send_sms = $request['id_send_sms'];

            $listcustom = DB::connection('mysql2')->table($nameTable)
                                ->where('mailing_file_original', $mailing_file_original)
                                ->where('mailing_file_genion', $mailing_file_genion)
                                ->where('id_campaign', $id_campaign)
                                ->where('id_client', $id_client)
                                ->where('id_send_sms', $id_send_sms)
                                ->get()
                                ->toArray();

            foreach ($listcustom as $value) {

                if(!$validClientJustSMS){

                    $ids[] = $value->id;
                    $to[] = [
                        "id"=> ($value->id),
                        "reference"=> ($value->id),
                        "phone"=> $value->ddd.$value->phone,
                        "message" => $value->message_sms. ' '. env('URL_SHORTENER_'.env('ENVIRONMENT')) . $value->hash
                    ];

                }else{

                    $ids[] = $value->id;
                    $to[] = [
                        "id"=> ($value->id),
                        "reference"=> ($value->id),
                        "phone"=> $value->ddd.$value->phone,
                        "message" => $value->message_sms,
                    ];
                }
            }

            if ($request['send_sms'] && count($to)) {
                $shippingBatch['id_client'] = $id_client;
                $shippingBatch['id_campaign'] = $id_campaign;
                $shippingBatch['ids'] = $ids;
                $shippingBatch['nameTable'] = $nameTable;
                $shippingBatch['mailing_file_original'] = $mailing_file_original;
                $shippingBatch['mailing_file_genion'] = $mailing_file_genion;
                $shippingBatch['id_send_sms'] = $id_send_sms;
                $chunk = array_chunk($to, env('LIMIT_QUEUE_SMS'));
                foreach ($chunk as $key => $to) {
                    $shippingBatch['custom'] = $to;
                    $nameHash = $id_campaign . Carbon::now()->format('YmdHis').$key;
                    $hashQueue = hash("crc32", $nameHash);
                    SendSmsJob::dispatch($shippingBatch, 0, auth('api')->user())->onQueue($hashQueue);
                    QueueJobs::dispatch($hashQueue);

                }
            }

        } catch (Exception $e) {
            $this->fail($e->getMessage());
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
