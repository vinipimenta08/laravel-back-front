<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\BrokerSms;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BrokerSmsController extends Controller
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
            $broker = new BrokerSms;
            $broker = $broker->select('id', 'name', 'active')->get();
            return response()->json(LibraryController::responseApi($broker));
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
            $rules = [
                "name" => "required"
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $broker = new BrokerSms;
            $broker->fill($request->all());
            $broker->save();
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
            $broker = new BrokerSms;
            $broker = $broker->findOrFail($id);
            return response()->json(LibraryController::responseApi($broker));
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
            $rules = [
                "name" => "required"
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $user = auth('api')->user();
            $broker = new BrokerSms;
            $broker = $broker->findOrFail($id);
            $broker = $broker->fill($request->all());
            LibraryController::logupdate($broker);
            $broker->save();
            return response()->json(LibraryController::responseApi($broker, 'ok'));
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
            $broker = new BrokerSms;
            $broker = $broker->findOrFail($id);
            $broker->active = 0;
            $broker->save();
            $broker = $broker->delete();
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

    public function active(Request $request)
    {
        try {
            $user = auth('api')->user();
            $broker = new BrokerSms;
            $broker = $broker->findOrFail($request->id);

            if ($broker->active == 1) {
                $broker->active = 0;
            }else{
                $broker->active = 1;
            }
            LibraryController::logupdate($broker);
            $broker->update();
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
