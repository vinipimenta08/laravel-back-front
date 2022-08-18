<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Jobs\ImportListCustomJob;
use App\Jobs\QueueJobs;
use App\Jobs\ImportMailingJob;
use App\Jobs\ProgramListCustomJob;
use App\Jobs\ProgramMailingJob;
use App\Models\Campaigns;
use App\Models\Clients;
use App\Models\ListCustom;
use App\Models\ListHash;
use App\Models\LogImport;
use App\Models\LogImportError;
use App\Models\LogSshExport;
use App\Models\ReplySms;
use App\Models\UserClient;
use App\Models\MailingProcess;
use Carbon\Carbon;
use DateTime;
use EllGreen\LaravelLoadFile\Laravel\Facades\LoadFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function Psy\debug;

class ListCustomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function validateListCustom(Request $request)
    {
        try {
            $libraryController = new LibraryController;
            $errorImport = [
                'invalids' => []
            ];
            $count = 0;
            foreach ($request->custom as $key => $value) {
                $returnImport = $libraryController->validListCustom($value, $key);
                if ($returnImport[1]) {
                    $errorImport['invalids'][] = $returnImport[0];
                    $count += $returnImport[1];
                }
            }
            $errorImport['errors_validate'] = $count;
            return response()->json(LibraryController::responseApi($errorImport, 'ok'));
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

    public function uploadfilecustom(Request $request)
    {
        try {
            $user = auth('api')->user();
            $campaignsCount = Campaigns::where('id', $request->campaign);
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $campaignsCount = $campaignsCount->whereIn('id_client', $userClient);
                } else {
                    $campaignsCount = $campaignsCount->where('id_client', $user->id_client);
                }
            }
            $campaignsCount = $campaignsCount->get()->count();
            if ($campaignsCount == 0) {
                return response()->json(LibraryController::responseApi([],'Campaign not found', 100));
            }

            $queue['campaign'] = $request->campaign;
            $queue['send_sms'] = $request->send_sms;
            $queue['custom'] = $request->custom;
            $nameHash = $request->campaign. Carbon::now()->format('YmdHi');
            $hashQueue = hash("crc32",$nameHash);
            ImportMailingJob::dispatch($queue, 0, auth('api')->user())->onQueue($hashQueue);
            QueueJobs::dispatch($hashQueue);

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

    public function statuscustomspecific(Request $request, $id_campaign)
    {
        try {
            $user = auth('api')->user();
            $campaigns = Campaigns::where('id', $id_campaign);
            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $campaigns = $campaigns->whereIn('campaigns.id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $campaigns = $campaigns->where('campaigns.id_client', $user->id_client);
                }
            }
            $campaigns = $campaigns->get();
            $statusListCustom = [];
            foreach ($campaigns as $key => $value) {
                $listCustom = ListCustom::where('id_campaign', $value->id);
                $logImportError = LogImportError::where('id_campaigns', $value->id);
                $listCustom = $listCustom->select('id_client', 'id_campaign', DB::raw('COUNT(id) imported, SUM(CASE WHEN list_custom.id_send_sms = 1 THEN 1 WHEN list_custom.id_send_sms <> 1 THEN 0 END) sended'))->groupBy('id_client', 'id_campaign')->first();
                $logImportError = $logImportError->select(DB::raw('COUNT(*) failed'))->first();
                $statusListCustom[] = [
                    'id_client' => $value->id_client,
                    'id_campaign' => $value->id,
                    'base' => ((isset($listCustom['imported']) ? $listCustom['imported'] : 0) + (isset($logImportError['failed']) ? $logImportError['failed']: 0)),
                    'imported' => (isset($listCustom['imported']) ? $listCustom['imported'] : 0),
                    'failed' => (isset($logImportError['failed']) ? $logImportError['failed']: 0),
                    'sended' => (int)(isset($listCustom['sended']) ? $listCustom['sended'] : 0),
                    'campaign' => $value
                ];
            }
            return response()->json(LibraryController::responseApi($statusListCustom));
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

    public function statuscustom(Request $request)
    {
        try {
            $libraryController = new LibraryController;
            if (isset($request->date_init)) {
                if (!$libraryController->validateDate($request->date_init)) {
                    return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                }
            }else {
                $request->merge([
                    'date_init' => Carbon::now()->format('Y-m-d')
                ]);
            }
            if (isset($request->date_end)) {
                if (!$libraryController->validateDate($request->date_end)) {
                    return response()->json(LibraryController::responseApi([], 'invalid date', 100));
                }
            }else {
                $request->merge([
                    'date_end' => Carbon::now()->format('Y-m-d')
                ]);
            }
            $data1 = Carbon::createFromDate($request->date_init);
            $data2 = Carbon::createFromDate($request->date_end);
            if ($data2->lt($data1)) {
                return response()->json(LibraryController::responseApi([], 'invalid date', 100));
            }

            $user = auth('api')->user();

            $init_date = new DateTime($request->date_init);
            $end_date = new DateTime($request->date_end);

            $dateRange = array();
            while($init_date <= $end_date){
                $dateRange[] = $init_date->format('Ymd');
                $init_date = $init_date->modify('+1day');
            }

            $nameTable = "";
            $statusListCustom = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    if (isset($request->id_campaign) && $request->id_campaign) {
                        $campaigns = Campaigns::where('campaigns.id', $request->id_campaign);
                    }else {
                        $campaigns = new Campaigns;
                    }
                    if ($user->alternative_profile) {
                        $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                        $campaigns = $campaigns->whereIn('campaigns.id_client', $userClient);
                    }else {
                        if ($user->id_profile != 1) {
                            $campaigns = $campaigns->where('campaigns.id_client', $user->id_client);
                        }
                    }
                    $campaigns = $campaigns->join('clients', 'campaigns.id_client', '=', 'clients.id');
                    $campaigns = $campaigns->where(function($query) use ($request, $nameTable) {
                        $query->whereNotNull(DB::raw("(SELECT id FROM $nameTable AS list_custom WHERE list_custom.id_campaign = campaigns.id and list_custom.created_at Between '$request->date_init 00:00:00' AND '$request->date_end  23:59:59'  limit 1)"));
                        $query->orWhereNotNull(DB::raw("(SELECT id FROM log_import_errors WHERE log_import_errors.id_campaigns = campaigns.id and log_import_errors.created_at Between '$request->date_init 00:00:00' AND '$request->date_end 23:59:59' limit 1)"));
                    });
                    $campaigns = $campaigns->select('campaigns.*', DB::raw('clients.name as name_client'))->distinct()->get();

                    $statusListCustom = [];
                    foreach ($campaigns as $key => $value) {

                        $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom")
                                            ->where('id_campaign', $value->id)
                                            ->whereBetween('created_at', [($request->date_init . ' 00:00:00'), ($request->date_end . ' 23:59:59')])
                                            ->select('id_client', 'id_campaign', DB::raw('max(sended_at) as sended_at'), DB::raw('COUNT(id) imported, SUM(CASE WHEN list_custom.id_send_sms = 1 THEN 1 WHEN list_custom.id_send_sms <> 1 THEN 0 END) sended'), DB::raw('SUM(CASE WHEN list_custom.id_status_link = 2 THEN 1 WHEN list_custom.id_status_link <> 2 THEN 0 END) opening'))
                                            ->groupBy('id_client', 'id_campaign')
                                            ->first();

                        $logImportError = LogImportError::where('id_campaigns', $value->id)
                                                            ->whereBetween('created_at', [($request->date_init . ' 00:00:00'), ($request->date_end . ' 23:59:59')])
                                                            ->select(DB::raw('COUNT(*) failed'))->first();

                        $replySms = ReplySms::join($nameTable.' AS list_custom', 'list_custom.id', '=', 'reply_sms.id_list_custom')
                                            ->where('list_custom.id_campaign', $value->id)
                                            ->whereBetween('reply_sms.created_at', [($request->date_init . ' 00:00:00'), ($request->date_end . ' 23:59:59')])
                                            ->select('list_custom.id_campaign', DB::raw('COUNT(DISTINCT reply_sms.id) as reply'))
                                            ->groupBy('list_custom.id_campaign')
                                            ->first();

                        // if (((isset($listCustom['imported']) ? $listCustom['imported'] : 0) + (isset($logImportError['failed']) ? $logImportError['failed']: 0)))
                        $statusListCustom[] = [
                            'id_client' => $value->id_client,
                            'name_client' => $value->name_client,
                            'base' => ((isset($listCustom->imported) ? $listCustom->imported : 0) + (isset($logImportError['failed']) ? $logImportError['failed']: 0)),
                            'imported' => (isset($listCustom->imported) ? $listCustom->imported : 0),
                            'failed' => (isset($logImportError['failed']) ? $logImportError['failed']: 0),
                            'sended' => (int)(isset($listCustom->sended) ? $listCustom->sended : 0),
                            'opening' => (int)(isset($listCustom->opening) ? $listCustom->opening : 0),
                            'reply' => (isset($replySms['reply']) ? $replySms['reply'] : 0),
                            'sended_at' => (isset($listCustom->sended_at) ? $listCustom->sended_at : 0),
                            'campaign' => [
                                'id'=> $value->id,
                                'name'=> $value->name
                            ]
                        ];
                    }
                }
            }

            return response()->json(LibraryController::responseApi($statusListCustom));
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

    public function greateropening(Request $request)
    {
        try {
            $user = auth('api')->user();

            $date_init = new DateTime($request->date_init);
            $date_end = new DateTime($request->date_end);

            $dateRange = array();
            while($date_init <= $date_end){
                $dateRange[] = $date_init->format('Ymd');
                $date_init = $date_init->modify('+1day');
            }

            $responselistCustom = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {

                    $data = Carbon::parse($row)->format('Y-m-d');

                    $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom")->where('list_custom.id_send_sms', 1);

                    $listCustom = $listCustom->select('id_client', 'id_campaign', DB::raw("case
                                                WHEN TRIM(list_custom.ddd) in(68) THEN 'AC'
                                                WHEN TRIM(list_custom.ddd) in(82) THEN 'AL'
                                                WHEN TRIM(list_custom.ddd) in(97,92) THEN 'AM'
                                                WHEN TRIM(list_custom.ddd) in(96) THEN 'AP'
                                                WHEN TRIM(list_custom.ddd) in(77,75,73,74,71) THEN 'BA'
                                                WHEN TRIM(list_custom.ddd) in(88,85) THEN 'CE'
                                                WHEN TRIM(list_custom.ddd) in(61) THEN 'DF'
                                                WHEN TRIM(list_custom.ddd) in(28,27) THEN 'ES'
                                                WHEN TRIM(list_custom.ddd) in(62,64,61) THEN 'GO'
                                                WHEN TRIM(list_custom.ddd) in(99,98) THEN 'MA'
                                                WHEN TRIM(list_custom.ddd) in(34,37,31,33,35,38,32) THEN 'MG'
                                                WHEN TRIM(list_custom.ddd) in(67) THEN 'MS'
                                                WHEN TRIM(list_custom.ddd) in(65,66) THEN 'MT'
                                                WHEN TRIM(list_custom.ddd) in(91,94,93) THEN 'PA'
                                                WHEN TRIM(list_custom.ddd) in(83) THEN 'PB'
                                                WHEN TRIM(list_custom.ddd) in(81,87) THEN 'PE'
                                                WHEN TRIM(list_custom.ddd) in(89,86) THEN 'PI'
                                                WHEN TRIM(list_custom.ddd) in(43,41,42,44,45,46) THEN 'PR'
                                                WHEN TRIM(list_custom.ddd) in(24,22,21) THEN 'RJ'
                                                WHEN TRIM(list_custom.ddd) in(84) THEN 'RN'
                                                WHEN TRIM(list_custom.ddd) in(69) THEN 'RO'
                                                WHEN TRIM(list_custom.ddd) in(95) THEN 'RR'
                                                WHEN TRIM(list_custom.ddd) in(53,54,55,51) THEN 'RS'
                                                WHEN TRIM(list_custom.ddd) in(47,48,49) THEN 'SC'
                                                WHEN TRIM(list_custom.ddd) in(79) THEN 'SE'
                                                WHEN TRIM(list_custom.ddd) in(11,12,13,14,15,16,17,18,19) THEN 'SP'
                                                WHEN TRIM(list_custom.ddd) in(63) THEN 'TO'
                                                end estado"),
                                    DB::raw("count(*)total"))
                                ->groupBy('id_client')
                                ->groupBy('id_campaign')
                                ->groupBy('estado')
                                ->orderBy('total', 'desc');

                    if ($user->id_profile != 1) {
                        $listCustom = $listCustom->where('list_custom.id_client', $user->id_client);
                    }else {
                        if(isset($request->id_client)){
                            $listCustom = $listCustom->where('list_custom.id_client', $request->id_client);
                        }
                    }

                    $listCustom = $listCustom->whereBetween('list_custom.sended_at', [($data . ' 00:00:00'), ($data . ' 23:59:59')]);

                    $responselistCustom[] = $listCustom->get()->toArray();

                }
            }

            $total = 0;

            foreach ($responselistCustom as $key => $value) {
                foreach ($value as $key => $row) {
                    $total += $row->total;
                }
            }

            $response = array();
            foreach ($responselistCustom as $key => $value) {
                if ($value) {
                    foreach ($value as $key => $row) {
                        $response[$row->estado]['estado'] = $row->estado;
                        $response[$row->estado]['percent'] = round(((100 * $row->total) / $total),2);
                    }
                }
            }

            return response()->json(LibraryController::responseApi($response));
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

    public function LoadFile(Request $request)
    {

        $file_name_original = $request->file_name_original;
        $file_name_genion = $request->file_name_genion;
        $id_campaign = $request->id_campaign;
        $id_client = $request->id_client;

        $library = new LibraryController;
        $just_sms = $library->validClientJustSMS();

        // LOAD FILE
        if($just_sms){
            LoadFile::connection("mysql2")
                    ->file(base_path() ."/../frontend/storage/app/upload/". $file_name_genion, $local = true)
                    ->into("mailing_process")
                    ->columns(['phone', "message_sms"])
                    ->fieldsTerminatedBy(';')
                    ->linesTerminatedBy('\n')
                    ->ignoreLines(1)
                    ->set([
                        'id_send_sms' => '3',
                        'mailing_file_original' => $file_name_original,
                        'mailing_file_genion' => $file_name_genion,
                        'created_at' => DB::raw('NOW()'),
                        'id_client' => $id_client,
                        'id_campaign' => $id_campaign,
                        'ddd' => DB::raw("substring(phone,1,2)"),
                        'phone' => DB::raw("substring(phone,3,10)"),
                        'date_event' => Carbon::now()->format('Y-m-d')
                    ])
                    ->load();

        }else{

            LoadFile::connection("mysql2")
                    ->file(base_path() ."/../frontend/storage/app/upload/". $file_name_genion, $local = true)
                    ->into("mailing_process")
                    ->columns(['phone', "message_sms", "title", "date_event", "description", "location", "identification", "joker_one", "joker_two"])
                    ->fieldsTerminatedBy(';')
                    ->linesTerminatedBy('\n')
                    ->ignoreLines(1)
                    ->set([
                        'id_send_sms' => '3',
                        'mailing_file_original' => $file_name_original,
                        'mailing_file_genion' => $file_name_genion,
                        'created_at' => DB::raw('NOW()'),
                        'id_client' => $id_client,
                        'id_campaign' => $id_campaign,
                        'date_event' => DB::raw("DATE_FORMAT(STR_TO_DATE(date_event, '%d/%m/%Y'), '%Y-%m-%d')"),
                        'ddd' => DB::raw("substring(phone,1,2)"),
                        'phone' => DB::raw("substring(phone,3,10)"),
                    ])
                    ->load();

        }

        return response()->json(LibraryController::responseApi("", 'ok'));
    }

    public function validateLoadFile(Request $request)
    {

        $file_name_original = $request->file_name_original;
        $file_name_genion = $request->file_name_genion;
        $id_campaign = $request->id_campaign;
        $id_client = $request->id_client;

        $return = MailingProcess::where('id_client', $id_client)
                                ->where('id_campaign', $id_campaign)
                                ->where('mailing_file_original', $file_name_original)
                                ->where('mailing_file_genion', $file_name_genion)
                                ->get();


        $errorImport = [];

        $errorImportReturn = [
            'fields' => [],
            'line' => 0
        ];
        $count = 0;

        $library = new LibraryController;
        $just_sms = $library->validClientJustSMS();

        $key = 0;
        foreach ($return as $row) {
            $key = $key + 1;
            $erro = 0;
            $errorCount = 0;

            try{

                if($just_sms){

                    if ((strlen($row['phone']) != 9) || (substr($row['phone'], 0, 1) != 9)) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "telefone|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "telefone", 'value' => $row['phone']];
                    }

                    $size_sms = strlen(rtrim(trim($row['message_sms'])));
                    if ($size_sms > 160) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "mensagem_sms|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "mensagem_sms", 'value' => $row['message_sms']];
                    }

                    if ($erro > 0) {

                        $date_import = Carbon::now()->format('Ymd-His');
                        $library = new LibraryController;
                        $library->errorimport($file_name_genion, $errorImport['line'], substr($errorImport['fields'], 0, -1), $errorImport['count'], $id_campaign, $date_import, $id_client);

                    }


                }else{

                    // $errorImportReturn['line'] = $key;

                    if ((strlen($row['phone']) != 9) || (substr($row['phone'], 0, 1) != 9)) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "telefone|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "telefone", 'value' => $row['phone']];
                    }

                    $size_sms = strlen(rtrim(trim($row['message_sms'])));
                    if ($size_sms > 160) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "mensagem_sms|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "mensagem_sms", 'value' => $row['message_sms']];
                    }

                    if (!$library->validateDate($row['date_event'])) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "data_inicio|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "data_inicio", 'value' => $row['date_event']];
                    }
                    $tamanho_titulo = strlen(trim($row['title']));
                    if ($tamanho_titulo > 50) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "titulo_evento|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "titulo_evento", 'value' => $row['title']];
                    }
                    $tamanho_descricao = strlen(trim($row['description']));
                    if ($tamanho_descricao > 300) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "descricao|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "descricao", 'value' => $row['description']];
                    }
                    $tamanho_localizacao = strlen(trim($row['location']));
                    if ($tamanho_localizacao > 100) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "localizacao|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "localizacao", 'value' => $row['location']];
                    }
                    $tamanho_identificador = strlen(trim($row['identification']));
                    if ($tamanho_identificador > 50) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "identificador|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "identificador", 'value' => $row['identification']];
                    }
                    $tamanho_coringa1 = strlen(trim($row['joker_one']));
                    if ($tamanho_coringa1 > 50) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "coringa_1|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "coringa_1", 'value' => $row['joker_one']];
                    }
                    $tamanho_coringa2 = strlen(trim($row['joker_two']));
                    if ($tamanho_coringa2 > 50) {
                        $count++;
                        $erro++;
                        $errorImport['line'] = ($key);
                        $errorImport['fields'] = "coringa_2|";
                        $errorImport['count'] = $erro;
                        $errorImportReturn['line'] = $key;
                        $errorImportReturn['fields'][] = ['field' => "coringa_2", 'value' => $row['joker_two']];
                    }

                    if ($erro > 0) {

                        $date_import = Carbon::now()->format('Ymd-His');
                        $library = new LibraryController;
                        $library->errorimport($file_name_genion, $errorImport['line'], substr($errorImport['fields'], 0, -1), $errorImport['count'], $id_campaign, $date_import, $id_client);

                    }
                }

            }catch (Exception $e) {
                $logSshExport['type'] = 2;
                $logSshExport['message'] = $e->getMessage();
                $logSshExport['description'] = $e->getTraceAsString();
                LogSshExport::create($logSshExport);
            }
        }

        $returnImport['invalids'][] = $errorImportReturn;
        $returnImport['errors_validate'] = $count;
        return response()->json(LibraryController::responseApi($returnImport, 'ok'));

    }

    public function deleteLoadFile(Request $request)
    {
        $mailing_file_original = $request->file_name_original;
        $mailing_file_genion = $request->file_name_genion;
        $id_campaign = $request->id_campaign;
        $id_client = $request->id_client;

        DB::connection("mysql2")->table("log_import_errors")
                ->where('id_campaigns', $id_campaign)
                ->where('id_client', $id_client)
                ->where('name_file', $mailing_file_genion)
                ->delete();

        return response()->json(LibraryController::responseApi("", 'ok'));
    }

    public function uploadLoadFile(Request $request)
    {

        $mailing_file_original = $request->file_name_original;
        $mailing_file_genion = $request->file_name_genion;
        $id_campaign = $request->id_campaign;
        $id_client = $request->id_client;
        $check_envio_sms = $request->check_envio_sms;
        $check_agendamento_sms = $request->check_agendamento_sms;
        $check_verifyWhats = $request->check_verifyWhats;
        $check_verify_duplicate = $request->check_verify_duplicate;


        try {
            $user = auth('api')->user();

            if ($id_campaign == "") {
                return response()->json(LibraryController::responseApi([],'Campaign not found', 100));
            }

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
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }

    }

    public function programLoadFile(Request $request)
    {

        $mailing_file_original = $request->file_name_original;
        $mailing_file_genion = $request->file_name_genion;
        $id_campaign = $request->id_campaign;
        $id_client = $request->id_client;
        $check_envio_sms = $request->check_envio_sms;
        $check_agendamento_sms = $request->check_agendamento_sms;
        $check_verifyWhats = $request->check_verifyWhats;
        $check_verify_duplicate = $request->check_verify_duplicate;
        $date_schedule = $request->date_schedule;

        try {
            $user = auth('api')->user();

            if ($id_campaign == "") {
                return response()->json(LibraryController::responseApi([],'Campaign not found', 100));
            }

            $queue['mailing_file_original'] = $mailing_file_original;
            $queue['mailing_file_genion'] = $mailing_file_genion;
            $queue['id_campaign'] = $id_campaign;
            $queue['id_client'] = $id_client;
            $queue['check_envio_sms'] = $check_envio_sms;
            $queue['check_agendamento_sms'] = $check_agendamento_sms;
            $queue['check_verifyWhats'] = $check_verifyWhats;
            $queue['check_verify_duplicate'] = $check_verify_duplicate;
            $queue['date_schedule'] = $date_schedule;
            $queue['user'] = $user;
            $nameHash = $id_campaign. Carbon::now()->format('YmdHi');
            $hashQueue = hash("crc32",$nameHash);
            ProgramListCustomJob::dispatch($queue, 0, auth('api')->user())->onQueue($hashQueue);
            QueueJobs::dispatch($hashQueue);

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
