<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            $libraryController = new LibraryController;
            $clients = $libraryController->requestAsync('GET', '/api/clients');
            $brokers = $libraryController->requestAsync('GET', '/api/brokersms');
            return view('client.index', ['clients' => $clients['data'], 'brokers' => $brokers['data']]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        try {
            $rules = [
                'name' => 'required',
                'contact' => 'required|email',
                'password' => 'required|same:confirm_password',
                'confirm_password' => 'required',
                'brokers' => 'required'
            ];
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $libraryController = new LibraryController;
            $returnClient = $libraryController->requestAsync('POST', '/api/clients', $request->all());
            return $returnClient;
        }  catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleStoreError'), "message" => __('messages.defaultMessage')], "", 500);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $libraryController = new LibraryController;
            $client = $libraryController->requestAsync("GET", "/api/clients/$id");
            $brokers = $libraryController->requestAsync('GET', '/api/brokersms');
            $rules = [];

            $teste = explode(",", $client['data']['broker_sms']);

            foreach ($brokers['data'] as $key => $value) {
                foreach ($teste as $row) {
                    if ($value['id'] == $row) {
                        $rules[$value['id']] = $value['name'];
                    }
                }
            }
            return view('client.edit', ['client' => $client['data'], 'brokers' => $brokers['data'], 'rules' => $rules]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
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
                'name' => 'required',
                'contact' => 'required|email',
                'brokers' => 'required',
            ];
            if ($request->password || $request->confirm_password) {
                $rules['password'] = "required|same:confirm_password";
                $rules['confirm_password'] = "required";
            }
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $libraryController = new LibraryController;
            $client = $libraryController->requestAsync("PUT", "/api/clients/$id", $request->all());
            return $client;
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleUpdateError'), "message" => __('messages.defaultMessage')], "", 500);
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
            $libraryController = new LibraryController;
            $client = $libraryController->requestAsync("DELETE", "/api/clients/$id");
            return $client;
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDestroyError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function active(Request $request)
    {
        try {
            $data = [
                'id' => $request->id,
                'active' => $request->active == 'true' ? 1 : 0
            ];
            $libraryController = new LibraryController;
            $response = $libraryController->requestAsync("PUT", "/api/clients/active", $data);
            return response($response);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleActiveError', ["status" => ($request->active == 'true' ? 'ativar' : 'inativar')]), "message" => __('messages.defaultMessage')], "", 500);
        }
    }
}
