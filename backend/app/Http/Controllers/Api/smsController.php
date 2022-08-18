<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\ListCustom;
use App\Models\ReplySms;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery\Expectation;

class smsController extends Controller
{

    /**
     * Sending a single sms
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendonesms($request, $id = 1)
    {
        try {
            // id 1 - send by zenvia
            if ($id == 1) {
                $response = $this->sendsmszenvia($request->to[0]);
            }else if ($id == 2) {
                $response = $this->sendsmskolmeya($request->to);
            }else if ($id == 3) {
                $response = $this->sendsmstalkip($request->to[0]);
            }else {
                $code = 500;
                $message = 'brokers not found';
                return response()->json(LibraryController::responseApi([], $message, $code));
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

    /**
     * Sending multiple sms.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendmultiplesms($request, $id = 1)
    {
        try {
            // id 1 - send by zenvia
            if ($id == 1) {
                $response = $this->sendmultiplesmszenvia($request->to);
            }else if ($id == 2) {
                $response = $this->sendsmskolmeya($request->to);
            }else if ($id == 3) {
                $response = $this->sendsmstalkip($request->to, 'multiple');
            }else {
                $code = 500;
                $message = 'brokers not found';
                return response()->json(LibraryController::responseApi([], $message, $code));
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

    /**
     * INTEGRATION SMS ZENVIA.
     *
     */
    public function sendsmszenvia($to, $callbackOption = 'NONE')
    {
        try {
            $head = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . env('KEY_ZENVIA'),
                'Accept' => 'application/json'
            ];
            $body = [
                'sendSmsRequest' => [
                    'from'              => "",
                    'to'                => '55'.$to['phone'],
                    'msg'               => $to['message'],
                    'id'                => $to['id'],
                    'callbackOption'    => $callbackOption,
                    'aggregateId'       => "102500",
                    'flashSms'          => false
                ],
            ];

            $response = Http::timeout(30)->withHeaders($head)->post('https://api-rest.zenvia.com/services/send-sms',$body);
            return $response->json();
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return $smszenvia['sendSmsResponse']['statusCode'] = 01;
        }
    }

    public function sendmultiplesmszenvia($to, $callbackOption = 'NONE')
    {
        try {
            $tos = [];
            foreach ($to as $key => $value) {
                array_push($tos, [
                                'id'                => $value['id'],
                                'to'                => '55' . $value['phone'],
                                'msg'               => $value['message'],
                                'callbackOption'    => $callbackOption,
                                'flashSms'          => false
                            ]);
            }
            $head = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . env('KEY_ZENVIA'),
                'Accept' => 'application/json'
            ];

            $body = [
                'sendSmsMultiRequest' => [
                    'aggregateId' => 102500,
                    'sendSmsRequestList' => $tos,
                ],
            ];
            $response = Http::timeout(30)->withHeaders($head)->post('https://api-rest.zenvia.com/services/send-sms-multiple',$body);
            return $response->json();
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return $smszenvia['sendSmsResponse']['statusCode'] = 01;
        }
    }

    /**
     * INTEGRATION SMS KOLMEYA.
     *
     */
    public function sendsmskolmeya($sms)
    {
        try {
            $head = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '. env('KEY_KOLMEYA')
            ];
            $body = [
                'reference' => "campanha-".date('d-m-Y'),
                'messages' => $sms
            ];
            $response = Http::timeout(30)->withHeaders($head)->post('https://kolmeya.com.br/api/v1/sms/store',$body);
            if (isset($response['errors'])) {
                return $response['errors'];
            }
            return $response->json();
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return ['messages' => 'error'];
        }
    }

    public function responseKolmeya()
    {
        try {
            $head = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('KEY_KOLMEYA')
            ];
            $body = [
                        'period' => 1
                    ];
            $response = Http::timeout(30)->withHeaders($head)->post('https://kolmeya.com.br/api/v1/sms/replys',$body);
            foreach ($response->json()['data'] as $key => $value) {
                $filtroSms = ReplySms::where('id_sms', $value['id'])->count();
                $filtroReference = ListCustom::where('id_sms', $value['message']['id'])->count();
                $listCustom = ListCustom::where('id_sms', $value['message']['id'])->first();
                if ($filtroSms == 0 && $filtroReference > 0) {
                    $received_at = str_replace('/','-',$value['received_at']);
                    $data = date('Y-m-d H:i:s',  strtotime($received_at));
                    $replySms = new ReplySms;
                    $replySms->id_sms = $value['id'];
                    $replySms->id_list_custom = $listCustom->id;
                    $replySms->phone = $value['message']['phone'];
                    $replySms->id_reference = $value['message']['id'];
                    $replySms->reply = $value['reply'];
                    $replySms->received_at = $data;
                    $replySms->save();
                }
            }
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

    public function statussmskolmeya(Request $request)
    {
        try {
            $head = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('KEY_KOLMEYA')
            ];
            $body = [
                        'id' => $request->id
                    ];
            $response = Http::timeout(30)->withHeaders($head)->post('https://kolmeya.com.br/api/v1/sms/status/message',$request->all());
            return response()->json(LibraryController::responseApi($response->json()));
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


    /**
     * INTEGRATION SMS TALKIP.
     *
     */
    public function sendsmstalkip($to, $send = 'one')
    {
        try {
            $head = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic '. env('KEY_TALKIP')
            ];
            if($send == "one"){
                $body = [
                    'phone'     => $to['phone'],
                    'message'   => $to['message'],
                ];

                $response = Http::timeout(150)->withHeaders($head)->post('http://142.93.78.16/api/sms', $body);

            }else{

                $body = [
                    'block' => $to,
                ];

                $response = Http::timeout(150)->withHeaders($head)->post('http://142.93.78.16/api/blocks', $body);

            }
            return $response->json();

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

    public function statusSmsTalkip(Request $request, $send = 'multiple')
    {
        try {
            $head = [
                'Content-type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Basic '. env('KEY_TALKIP')
            ];
            if($send == "one"){
                $url = 'http://142.93.78.16/api/sms/'. $request->id;
            }else{
                $url = 'http://142.93.78.16/api/blocks/'. $request->id;
            }

            $response = Http::timeout(150)->withHeaders($head)->get($url);

            return $response->json();

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

    public function responseSmsTalkip($id)
    {
        try {
            $head = [
                'Content-type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Basic '. env('KEY_TALKIP')
            ];

            $url = 'http://142.93.78.16/api/sms/'. $id .'/responses';
            $response = Http::timeout(150)->withHeaders($head)->get($url);

            return $response->json();

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return response()->json($e->getMessage());
        }
    }


    public static function responseMailingTalkip()
    {
        try{

            $dataAtual = Carbon::now()->format('Ymd');

            $nameTable = 'list_custom_'.$dataAtual;

            $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');
            if ($query) {
                $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom");
                $listCustom = $listCustom->whereNotNull("id_sms");
                $returnListCustom = $listCustom->get()->toArray();

                $library = new LibraryController;
                $smsController = new smsController;
                foreach ($returnListCustom as $row) {
                    $just_sms = $library->validJustSMS($row->id_client);

                    if ($just_sms) {
                        $partIdSns = explode("_", $row->id_sms);

                        if (count($partIdSns) >= 3) {
                            $id = $partIdSns[0];

                            $response = $smsController->responseSmsTalkip($id);

                            foreach ($response as $key => $value) {
                                if (is_array($value)) {
                                    $reply = new ReplySms();

                                    $validationReply = $reply->where("id_sms", $row->id_sms);
                                    $validationReply = $validationReply->where("reply", $value['content']);
                                    $responseReply = $validationReply->get()->toArray();

                                    if (count($responseReply) == 0) {
                                        $reply->id_reference = $id;
                                        $reply->phone = $row->ddd.$row->phone;
                                        $reply->id_sms = $row->id_sms;
                                        $reply->reply = $value['content'];
                                        $reply->received_at = $value['receivedAt'];
                                        $reply->id_list_custom = $row->id;
                                        $reply->save();
                                    }

                                }
                            }

                        }

                    }

                }

            }

            return response()->json(LibraryController::responseApi("", 'ok'));

        } catch (Exception $e) {
            Log::debug('Log: ' . $e);
        }
    }

}
