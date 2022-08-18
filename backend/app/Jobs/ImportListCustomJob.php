<?php

namespace App\Jobs;

use App\Http\Controllers\Api\CheckExistsWhatsappController;
use App\Http\Controllers\LibraryController;
use App\Models\Campaigns;
use App\Models\CheckExistsWhatsApp;
use App\Models\Clients;
use App\Models\ListCustom;
use App\Models\ListHash;
use App\Models\LogImport;
use App\Models\LogImportError;
use App\Models\MailingProcess;
use Carbon\Carbon;
use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;
use Exception;
use Facade\FlareClient\Http\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ImportListCustomJob implements ShouldQueue
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
            $validClientJustSMS = $this->validClientJustSMS($request['id_client']);

            $mailing_file_original = $request['mailing_file_original'];
            $mailing_file_genion = $request['mailing_file_genion'];
            $id_campaign = $request['id_campaign'];
            $id_client = $request['id_client'];
            $check_envio_sms = $request['check_envio_sms'];
            $check_agendamento_sms = $request['check_agendamento_sms'];
            $check_verifyWhats = $request['check_verifyWhats'];
            $check_verify_duplicate = $request['check_verify_duplicate'];
            $user = $request['user'];

            $query = DB::connection('mysql2')->select("call optin_find_errors(". $id_client .", " . $id_campaign . ", '" . $mailing_file_original . "','" . $mailing_file_genion . "', " . $validClientJustSMS . ")");

            // IMPORT DATA LIST CUSTOM
            $mailing = MailingProcess::where('mailing_file_original', $mailing_file_original)
                        ->where('mailing_file_genion', $mailing_file_genion)
                        ->where('id_campaign', $id_campaign)
                        ->where('id_client', $id_client)
                        ->where('confirm_imported', 0)
                        ->get()
                        ->toArray();

            // IMPORT ERRORS
            $error = LogImportError::select(DB::raw("(line_file - 1) as line_file"))
                        ->where('name_file', $mailing_file_genion)
                        ->get()
                        ->toArray();

            foreach ($error as $value) {
                unset($mailing[$value['line_file']]);
            }

            if ($check_verify_duplicate == "true") {
                foreach ($mailing as $row) {
                    $phone[] = $row['ddd'].$row['phone'];
                }

                $duplicates = array_diff_assoc($phone, array_unique($phone));

                foreach ($duplicates as $key => $value) {
                    $mailing[$key]['id_send_sms'] = 18;
                }
            }

            $nameTable = "list_custom_".Carbon::now()->format('Ymd');

            $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

            if (!$query) {
                Schema::connection('mysql2')->create($nameTable, function (Blueprint $table) {
                    $table->id();
                    $table->string('mailing_file_original', 255);
                    $table->string('mailing_file_genion', 255);
                    $table->unsignedBigInteger('id_client');
                    $table->unsignedBigInteger('id_campaign');
                    $table->integer('ddd');
                    $table->integer('phone');
                    $table->string('message_sms', 170);
                    $table->date('date_event');
                    $table->string('title', 50);
                    $table->longText('description');
                    $table->string('location', 100)->nullable();
                    $table->string('joker_one')->nullable();
                    $table->string('joker_two')->nullable();
                    $table->string('identification')->comment('id de identificação do cliente');
                    $table->unsignedBigInteger('id_send_sms')->comment('status de enviado para o cliente do sms de envio');
                    $table->string('id_sms')->comment('id do lote de envio sms');
                    $table->unsignedBigInteger('id_status_link')->default(1);
                    $table->string('hash');
                    $table->boolean('active')->default(1);
                    $table->boolean('attempt')->default(0);
                    $table->timestamp('sended_at')->nullable();
                    $table->timestamps();

                    $table->index(['ddd', 'phone']);
                    $table->index(['created_at']);
                });
            }

            if(!$validClientJustSMS){
                // KOLMEYA E ZENVIA
                $subtitle = "MAILING_NAME_ORIGINAL,MAILING_NAME_GENION,ID_CLIENT;ID_CAMPANHA;DDD;CELULAR;MENSAGEM_SMS;TITULO_EVENTO;DATA_INICIO;DESCRICAO;LOCALIZACAO;IDENTIFICADOR;CORINGA_1;CORINGA_2;ID_SEND_SMS;ID_SMS;ID_STATUS_LINK;HASH;CREATE_AT";
                $jumpLine = "\n";

                $str = "";
                $str .= $subtitle.$jumpLine;
                foreach ($mailing as $row) {
                    $hash = hash("crc32", $row['id_client'] . $row['id_campaign'] . $row['id']);

                    $str .= $row['mailing_file_original'].";";
                    $str .= $row['mailing_file_genion'].";";
                    $str .= $row['id_client'].";";
                    $str .= $row['id_campaign'].";";
                    $str .= $row['ddd'].";";
                    $str .= $row['phone'].";";
                    $str .= $this->removeAccents($row['message_sms']).";";
                    $str .= $row['title'].";";
                    $str .= $row['date_event'].";";
                    $str .= $row['description'].";";
                    $str .= $row['location'].";";
                    $str .= $row['identification'].";";
                    $str .= $row['joker_one'].";";
                    $str .= rtrim($row['joker_two'], "\r").";";
                    $str .= $row['id_send_sms'].";";
                    $str .= $row['id_sms'].";";
                    $str .= $row['id_status_link'].";";
                    $str .= $hash.";";
                    $str .= Carbon::now()->format('Y-m-d H:i:s');
                    $str .= $jumpLine;
                }

                $name_file = 'import_mailing_list_custom_'.$id_client.'_'.$id_campaign.'_'.Carbon::now()->format('YmdHi').".csv";

                file_put_contents("storage/app/".$name_file, $str);

                LoadFile::connection("mysql2")
                        ->file(base_path() ."/storage/app/".$name_file , $local = true)
                        ->into($nameTable)
                        ->columns(['mailing_file_original', 'mailing_file_genion', 'id_client', 'id_campaign', 'ddd', 'phone', "message_sms", "title", "date_event", "description", "location", "identification", "joker_one", "joker_two", 'id_send_sms', 'id_sms', 'id_status_link', 'hash', 'created_at'])
                        ->fieldsTerminatedBy(';')
                        ->linesTerminatedBy('\n')
                        ->ignoreLines(1)
                        ->load();

            }else{
                // TALKIP
                $subtitle = "MAILING_NAME_ORIGINAL,MAILING_NAME_GENION,ID_CLIENT;ID_CAMPANHA;DDD;CELULAR;MENSAGEM_SMS;DATA_INICIO;ID_SEND_SMS;ID_SMS;ID_STATUS_LINK;CREATE_AT";

                $jumpLine = "\n";

                $str = "";
                $str .= $subtitle.$jumpLine;
                foreach ($mailing as $row) {
                    $hash = hash("crc32", $row['id_client'] . $row['id_campaign'] . $row['id']);

                    $str .= $row['mailing_file_original'].";";
                    $str .= $row['mailing_file_genion'].";";
                    $str .= $row['id_client'].";";
                    $str .= $row['id_campaign'].";";
                    $str .= $row['ddd'].";";
                    $str .= $row['phone'].";";
                    $str .= $this->removeAccents($row['message_sms']).";";
                    $str .= $row['date_event'].";";
                    $str .= $row['id_send_sms'].";";
                    $str .= $row['id_sms'].";";
                    $str .= $row['id_status_link'].";";
                    $str .= Carbon::now()->format('Y-m-d H:i:s');
                    $str .= $jumpLine;
                }

                $name_file = 'import_mailing_list_custom_'.$id_client.'_'.$id_campaign.'_'.Carbon::now()->format('YmdHi').".csv";

                file_put_contents("storage/app/".$name_file, $str);

                LoadFile::connection("mysql2")
                        ->file(base_path() ."/storage/app/".$name_file , $local = true)
                        ->into($nameTable)
                        ->columns(['mailing_file_original', 'mailing_file_genion', 'id_client', 'id_campaign', 'ddd', 'phone', "message_sms", "date_event", 'id_send_sms', 'id_sms', 'id_status_link', 'created_at'])
                        ->fieldsTerminatedBy(';')
                        ->linesTerminatedBy('\n')
                        ->ignoreLines(1)
                        ->load();

            }

            LogImport::create([
                'id_user'       => $user['id'],
                'id_client'     => $id_client,
                'id_campaign'   => $id_campaign,
                'qtd_import'    => count($mailing),
                'send_sms'      => $check_envio_sms == "true" ? 1 : 0,
            ]);

            if(!$validClientJustSMS){

                // IMPORT DATA LIST HASH
                $listcustom = DB::connection('mysql2')->table($nameTable)
                            ->where('mailing_file_original', $mailing_file_original)
                            ->where('mailing_file_genion', $mailing_file_genion)
                            ->where('id_campaign', $id_campaign)
                            ->where('id_client', $id_client)
                            ->where('hash', "!=", "")
                            ->get()
                            ->toArray();

                $subtitle = "ID_LIST_CUSTOM;MAILING_NAME_ORIGINAL,MAILING_NAME_GENION,HASH;STATUS;CREATED_AT";
                $jumpLine = "\n";

                $send_sms = 0;

                $str = "";
                $str .= $subtitle.$jumpLine;
                foreach ($listcustom as $row) {
                    $str .= $row->id.";";
                    $str .= $row->mailing_file_original.";";
                    $str .= $row->mailing_file_genion.";";
                    $str .= $row->hash.";";
                    $str .= $send_sms.";";
                    $str .= Carbon::now()->format('Y-m-d H:i:s');
                    $str .= $jumpLine;
                }

                $name_file_hash = 'list_hash_'.$id_client.'_'.$id_campaign.'_'.Carbon::now()->format('YmdHi').".csv";

                file_put_contents("storage/app/".$name_file_hash, $str);

                LoadFile::connection("mysql2")
                        ->file(base_path() ."/storage/app/".$name_file_hash , $local = true)
                        ->into("list_hash")
                        ->columns(['id_list_custom', 'mailing_file_original', 'mailing_file_genion', 'hash', 'id_status', 'created_at'])
                        ->fieldsTerminatedBy(';')
                        ->linesTerminatedBy('\n')
                        ->ignoreLines(1)
                        ->load();

                shell_exec("rm -f ". base_path() ."/storage/app/".$name_file_hash);
            }

            shell_exec("rm -f ". base_path() ."/storage/app/".$mailing_file_genion);
            shell_exec("rm -f ". base_path() ."/storage/app/".$name_file);

            if ($check_verifyWhats == "true") {
                $checkWhats = CheckExistsWhatsappController::index();
                foreach ($checkWhats as $row) {
                    if ($row->response) {
                        CheckExistsWhatsApp::create([
                            'id_genion' => $row->idGenion,
                            'ddd' => substr($row->response->user, 2,2),
                            'phone' => substr($row->response->user, 4),
                        ]);
                    }
                }
            }

            if ($check_envio_sms == "true") {
                $queue['nameTable'] = $nameTable;
                $queue['mailing_file_original'] = $mailing_file_original;
                $queue['mailing_file_genion'] = $mailing_file_genion;
                $queue['id_campaign'] = $id_campaign;
                $queue['id_campaign'] = $id_campaign;
                $queue['id_client'] = $id_client;
                $queue['send_sms'] = $check_envio_sms == "true" ? 1 : 0;
                $queue['id_send_sms'] = 3;
                $nameHash = $id_campaign. Carbon::now()->format('YmdHi');
                $hashQueue = hash("crc32",$nameHash);
                ProcessMailingSmsJob::dispatch($queue, 0, auth('api')->user())->onQueue($hashQueue);
                QueueJobs::dispatch($hashQueue);
            }

            if ($check_agendamento_sms == "true") {
                DB::connection('mysql2')->table($nameTable)
                        ->where('mailing_file_genion', $mailing_file_genion)
                        ->where('id_campaign', $id_campaign)
                        ->where('id_client', $id_client)
                        ->where('id_send_sms', 3)
                        ->update([
                            'id_send_sms' => 17,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);
            }

            MailingProcess::where('mailing_file_original', $mailing_file_original)
                        ->where('mailing_file_genion', $mailing_file_genion)
                        ->where('id_campaign', $id_campaign)
                        ->where('id_client', $id_client)
                        ->update([
                            'confirm_imported' => 1,
                            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                        ]);

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

    public static function removeAccents($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë|ẽ)/","/(É|È|Ê|Ë|Ẽ)/","/(í|ì|î|ï|ĩ)/","/(Í|Ì|Î|Ï|Ĩ)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü|ũ)/","/(Ú|Ù|Û|Ü|Ũ)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$string);
    }

}
