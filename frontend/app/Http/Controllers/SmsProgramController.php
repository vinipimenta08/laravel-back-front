<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsProgramController extends Controller
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
            $smsProgram = $libraryController->requestAsync('GET', '/api/smsprogram');
            $campaigns = $libraryController->requestAsync('GET', '/api/campaigns');

            return view('sms_program.index', ['smsProgram' => $smsProgram['data'], 'campaigns' => $campaigns['data']]);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
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
            $rules = [
                "id_campaign" => "required",
                "programmed_at" => "required"
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $libraryController = new LibraryController;
            $returnSmsProgram = $libraryController->requestAsync('POST', '/api/smsprogram', $request->all());
            return response($returnSmsProgram);
        }  catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleStoreError'), "message" => __('messages.defaultMessage')], "", 500);
        }
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
            $smsProgram = $libraryController->requestAsync("GET", "/api/smsprogram/$id");
            $campaigns = $libraryController->requestAsync('GET', '/api/campaigns');
            return view('sms_program.edit', ['smsProgram' => $smsProgram['data'][0], 'campaigns' => $campaigns['data']]);
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
    public function update(Request $request)
    {
        try {
            $rules = [
                "programmed_at" => "required"
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }

            $id = $request->id;

            $libraryController = new LibraryController;
            $returnSmsProgram = $libraryController->requestAsync("PUT", "/api/smsprogram/$id", $request->all());

            return response($returnSmsProgram);
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
            $smsProgram = $libraryController->requestAsync("DELETE", "/api/smsprogram/$id");
            return response($smsProgram);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDestroyError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function active_sms_program(Request $request)
    {
        try {
            $data = [
                'id' => $request->id,
                'active' => $request->active == 'true' ? 1 : 0
            ];
            $libraryController = new LibraryController;
            $response = $libraryController->requestAsync("POST", "/api/smsprogram/active", $data);

            return response($response);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleActiveError', ["status" => ($request->active == 'true' ? 'ativar' : 'inativar')]), "message" => __('messages.defaultMessage')], "", 500);
        }
    }
}
