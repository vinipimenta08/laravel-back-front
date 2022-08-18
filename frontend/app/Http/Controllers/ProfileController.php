<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
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
            $user = $libraryController->requestAsync("POST", "/api/me");
            return view('profile.index', ['user' => $user]);
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
            $except[] = 'confirm_password';
            if (!$request->password) {
                $except[] = 'password';
            }
            $rules = [
                'password' => 'required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required_with:password'
            ];
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }

            $libraryController = new LibraryController;
            $responseUser = $libraryController->requestAsync('PUT', "/api/users/$id", $request->except($except));
            return response($responseUser);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleUpdateError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

}
