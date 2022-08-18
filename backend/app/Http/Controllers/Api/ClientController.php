<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\Clients;
use App\Models\CustomerBilling;
use App\Models\ListCustom;
use App\Models\UserClient;
use App\Models\ValueFire;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientController extends Controller
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
            $clients = new Clients;
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $clients = $clients->whereIn('id', $userClient);
                } else {
                    $clients = $clients->where('id', $user->id_client);
                }
            }
            $clients = $clients->select('id', 'name', 'contact', 'just_sms', 'active')->get();
            return response()->json(LibraryController::responseApi($clients));
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
            $rules['contact'] = [
                'required',
                'email',
                Rule::unique('mysql2.clients'),
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }

            $brokers = collect($request->brokers)->implode(',');

            $client = new Clients;
            $client->fill($request->all());
            $client->password = bcrypt($client->password);
            $client->broker_sms = $brokers;
            $client->save();
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
            $clients = new Clients;
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $clients = $clients->whereIn('id', $userClient);
                } else {
                    $clients = $clients->where('id', $user->id_client);
                }
            }
            $clients = $clients->findOrFail($id);
            return response()->json(LibraryController::responseApi($clients));
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
            $rules['contact'] = [
                'required',
                'email',
                Rule::unique('mysql2.clients')->ignore($id),
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }

            $brokers = collect($request->brokers)->implode(',');

            $user = auth('api')->user();
            $client = new Clients;
            $client = $client->findOrFail($id);
            $client = $client->fill($request->all());
            $client->password = bcrypt($client->contact);
            $client->broker_sms = $brokers;
            LibraryController::logupdate($client);
            $client->save();
            return response()->json(LibraryController::responseApi($client, 'ok'));
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
            $clients = new Clients;
            $clients = $clients->findOrFail($id);
            $clients = $clients->delete();
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

    public function valuesended(Request $request)
    {
        try {
            $libraryController = new LibraryController;
            if (isset($request->date_init)) {
                if (!$libraryController->validateDate($request->date_init)) {
                    return response()->json(LibraryController::responseApi([], 'invalid date', '100'));
                }
            } else {
                $request->merge([
                    'date_init' => Carbon::now()->format('Y-m-d')
                ]);
            }
            if (isset($request->date_end)) {
                if (!$libraryController->validateDate($request->date_end)) {
                    return response()->json(LibraryController::responseApi([], 'invalid date', '100'));
                }
            } else {
                $request->merge([
                    'date_end' => Carbon::now()->format('Y-m-d')
                ]);
            }
            $data1 = Carbon::createFromDate($request->date_init);
            $data2 = Carbon::createFromDate($request->date_end);
            if ($data2->lt($data1)) {
                return response()->json(LibraryController::responseApi([], 'invalid date', '100'));
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
            $listCustom = array();
            $resultListCustom = array();
            foreach ($dateRange as $row) {
                $nameTable = "list_custom_".$row;

                $query = DB::connection('mysql2')->select('SHOW TABLES LIKE "'.$nameTable.'";');

                if ($query) {
                    $listCustom = DB::connection("mysql2")->table($nameTable." AS list_custom");
                    if ($user->id_profile != 1) {
                        if ($user->alternative_profile) {
                            $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                            $listCustom = $listCustom->whereIn('list_custom.id_client', $userClient);
                        } else {
                            $listCustom = $listCustom->where('list_custom.id_client', $user->id_client);
                        }
                    } else {
                        if ($request->id_client)
                            $listCustom = $listCustom->where('list_custom.id_client', $request->id_client);
                    }

                    if (isset($request->search_campaign) && $request->search_campaign == 1) {
                        $listCustom = $listCustom->join('campaigns','list_custom.id_campaign', 'campaigns.id');
                        $listCustom = $listCustom->join('clients','list_custom.id_client', 'clients.id');
                        $resultListCustom[] = $listCustom->where('list_custom.id_send_sms', 1)
                            ->whereBetween('list_custom.created_at', [($request->date_init . " 00:00:00"), ($request->date_end . " 23:59:59")])
                            ->select('id_campaign', 'campaigns.name AS campaigns', 'clients.id AS id_client', 'clients.name AS clients', DB::raw('SUM(CASE WHEN list_custom.id_send_sms = 1 THEN 1 WHEN list_custom.id_send_sms <> 1 THEN 0 END) sended'))
                            ->groupBy('id_campaign')->groupBy('list_custom.id_client')->get()->toArray();
                    } else {
                        $listCustom = $listCustom->join('clients','list_custom.id_client', 'clients.id');
                        $resultListCustom[] = $listCustom->where('list_custom.id_send_sms', 1)
                            ->whereBetween('list_custom.created_at', [($request->date_init . " 00:00:00"), ($request->date_end . " 23:59:59")])
                            ->select('id_client', 'clients.name AS clients', DB::raw('SUM(CASE WHEN list_custom.id_send_sms = 1 THEN 1 WHEN list_custom.id_send_sms <> 1 THEN 0 END) sended'))
                            ->groupBy('id_client')->get()->toArray();
                    }

                }
            }
            $sended[]['sended'] = 0;
            if(count($resultListCustom) > 0 ){
                $return = array();
                foreach ($resultListCustom as $key => $value) {
                    foreach ($value as $row) {
                        $return[] = $row;
                    }
                }

                $result = array();
                foreach ($return as $key => $value) {
                    $valueFires = ValueFire::where('qtd_min', '<=', $value->sended)
                        ->where('qtd_max', '>=', $value->sended)
                        ->first();

                    $valuesended = isset($valueFires['value']) ? $valueFires['value'] : 0;

                    if (isset($request->search_campaign) && $request->search_campaign == 1) {
                        $result[$key]['id_campaign'] =  $value->id_campaign;
                        $result[$key]['campaigns'] =  $value->campaigns;
                        $result[$key]['id_client'] =  $value->id_client;
                        $result[$key]['clients'] =  $value->clients;
                        $result[$key]['sended'] =  $value->sended;
                        $result[$key]['valueSended'] = ($valuesended * $value->sended);
                    }else{
                        $result[$key]['id_client'] =  $value->id_client;
                        $result[$key]['clients'] =  $value->clients;
                        $result[$key]['sended'] =  $value->sended;
                        $result[$key]['valueSended'] = ($valuesended * $value->sended);
                    }

                    $customer = CustomerBilling::where("active", "=", 1)->get();
                    foreach ($customer as $row) {
                        if ($row['id_client'] == $value->id_client) {
                            $result[$key]['valueSended'] = $row['value'] * $value->sended;
                        }
                    }
                }

                $response = array();
                foreach ($result as $key => $value) {

                    if (isset($request->search_campaign) && $request->search_campaign == 1) {
                        $response[$value['id_campaign']]['id_campaign'] = $value['id_campaign'];
                        $response[$value['id_campaign']]['campaigns'] = $value['campaigns'];
                        $response[$value['id_campaign']]['id_client'] = $value['id_client'];
                        $response[$value['id_campaign']]['clients'] = $value['clients'];
                        @$response[$value['id_campaign']]['sended'] = $response[$value['id_campaign']]['sended'] + $value['sended'];
                        @$response[$value['id_campaign']]['valueSended'] = $response[$value['id_campaign']]['valueSended'] + ($valuesended * $value['sended']);
                    }else{
                        $response[$value['id_client']]['id_client'] =  $value['id_client'];
                        $response[$value['id_client']]['clients'] =  $value['clients'];
                        @$response[$value['id_client']]['sended'] = $response[$value['id_client']]['sended'] + $value['sended'];
                        @$response[$value['id_client']]['valueSended'] = $response[$value['id_client']]['valueSended'] + ($valuesended * $value['sended']);
                    }

                }
            }else{
                return response()->json(LibraryController::responseApi([], "Sem registro nesse período",  0));
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

    public function active(Request $request)
    {
        try {
            $client = Clients::where('id', $request->id)->first();
            $client->active = $request->active;
            LibraryController::logupdate($client);
            $client->update();
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
}
