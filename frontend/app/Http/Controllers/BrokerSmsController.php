<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrokerSmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if (!$request->request_server) {
                return redirect(route('layout'));
            }
            $libraryController = new LibraryController;
            $brokers = $libraryController->requestAsync("GET", "/api/brokersms");
            if ($brokers['error'] != 0) {
                return view('home');
            }
            return view('broker_sms.index',['brokers' => $brokers['data']]);
        } catch (Exception $e) {
            throw $e;
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
                'name' => 'required'
            ];
            $validate = Validator::make($request->all(), $rules);
            if ($validate->fails()) {
                return response(['status' => 'error', 'message' => $validate->getMessageBag()], 200);
            }
            $libraryController = new LibraryController;
            $responseUser = $libraryController->requestAsync('POST', '/api/brokersms', $request->all());
            return response($responseUser);
        } catch (Exception $e) {
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
            $brokerResponse = $libraryController->requestAsync("GET", "/api/brokersms/$id");
            $me = $libraryController->requestAsync("POST", "/api/me");
            $brokers = $brokerResponse;
            return view('broker_sms.edit', ['brokers' => $brokers['data']]);
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
                'name' => 'required'
            ];
            $validate = Validator::make($request->all(), $rules);
            if ($validate->fails()) {
                return response(['status' => 'error', 'message' => $validate->getMessageBag()], 200);
            }
            $libraryController = new LibraryController;
            $responseBroker = $libraryController->requestAsync('PUT', "/api/brokersms/$id", $request->all());
            return response($responseBroker);
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
            $brokers = $libraryController->requestAsync("DELETE", "/api/brokersms/$id");
            return response($brokers);
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
            $response = $libraryController->requestAsync("PUT", "/api/brokersms/active", $data);

            return response($response);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleActiveError', ["status" => ($request->active == 'true' ? 'ativar' : 'inativar')]), "message" => __('messages.defaultMessage')], "", 500);
        }
    }
}
