<?php

namespace App\Jobs;

use App\Http\Controllers\LibraryController;
use App\Models\Clients;
use App\Models\SmsSendingProgram;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProgramListCustomJob implements ShouldQueue
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
            $request = $this->request;

            $mailing_file_original = $request['mailing_file_original'];
            $mailing_file_genion = $request['mailing_file_genion'];
            $id_campaign = $request['id_campaign'];
            $id_client = $request['id_client'];
            $check_envio_sms = $request['check_envio_sms'];
            $check_agendamento_sms = $request['check_agendamento_sms'];
            $check_verifyWhats = $request['check_verifyWhats'];
            $check_verify_duplicate = $request['check_verify_duplicate'];
            $date_schedule = $request['date_schedule'];
            $user = $request['user'];

            SmsSendingProgram::create([
                'mailing_file_original' => $mailing_file_original,
                'mailing_file_genion' => $mailing_file_genion,
                'id_client' => $id_client,
                'id_campaign' => $id_campaign,
                'programmed_at' => $date_schedule,
            ]);

            $queue['mailing_file_original'] = $mailing_file_original;
            $queue['mailing_file_genion'] = $mailing_file_genion;
            $queue['id_campaign'] = $id_campaign;
            $queue['id_client'] = $id_client;
            $queue['check_envio_sms'] = $check_envio_sms;
            $queue['check_agendamento_sms'] = $check_agendamento_sms;
            $queue['check_verifyWhats'] = $check_verifyWhats;
            $queue['check_verify_duplicate'] = $check_verify_duplicate;
            $queue['campaign'] = $id_campaign;
            $queue['send_sms'] = $check_envio_sms == "true" ? 1 : 0;
            $queue['user'] = $user;
            $nameHash = $id_campaign. Carbon::now()->format('YmdHi');
            $hashQueue = hash("crc32",$nameHash);
            ImportListCustomJob::dispatch($queue, 0, auth('api')->user())->onQueue($hashQueue);
            QueueJobs::dispatch($hashQueue);

            return response()->json(LibraryController::responseApi([], 'ok'));

        } catch (Exception $e) {
            $this->fail($e->getMessage());
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
