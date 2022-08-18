<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetRequestController extends Controller
{
    public function sendPasswordResetEmail(Request $request){
        if(!$this->validEmail($request->email)) {
            return response()->json(LibraryController::responseApi([], '', 404));
        } else {
            $token = $this->generateToken($request->email);
            return response()->json(LibraryController::responseApi(['token' => $token], 'ok'));
        }

        return $this->validEmail($request->email);
    }

    public function validEmail($email) {
        return !!User::where('email', $email)->first();
    }

    public function generateToken($email){
      $isOtherToken = DB::table('recover_password')->where('email', $email)->first();
      if($isOtherToken) {
        return $isOtherToken->token;
      }
      $token = Str::random(80);
      $this->storeToken($token, $email);
      return $token;
    }

    public function storeToken($token, $email){
        DB::table('recover_password')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }
}
