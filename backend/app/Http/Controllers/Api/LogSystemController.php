<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\LogErros;
use App\Models\LogNavegation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogSystemController extends Controller
{
    public function logNavigation(Request $request)
    {
        try {
            $me = auth('api')->user();
            $request->merge([
                'id_user' => $me->id
            ]);
            $logNavegation = new LogNavegation;
            $logNavegation->fill($request->all());
            $logNavegation->save();
            return LibraryController::responseApi();
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi();
        }
    }

    public function recorderror(Request $request)
    {
        $me = auth('api')->user();
        $request->merge([
            'id_user' => $me->id
        ]);
        $logErros = new LogErros;
        $logErros->fill($request->all());
        $logErros->save();
    }
}
