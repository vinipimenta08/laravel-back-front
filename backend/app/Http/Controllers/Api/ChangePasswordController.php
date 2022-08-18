<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\RecoverPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChangePasswordController extends Controller
{

    public function index(Request $request){

        $recoverPassword = new RecoverPassword;
        $recoverPassword = $recoverPassword->where('token', $request->token)->get();

        return response()->json(LibraryController::responseApi($recoverPassword));
    }

    public function passwordResetProcess(Request $request){

        return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();
    }

    private function updatePasswordRow($request){
        return DB::table('recover_password')->where([
            'token' => $request->token
        ]);
    }

    private function tokenNotFoundError() {
        return response()->json(LibraryController::responseApi([], 'Seu e-mail ou token está errado.', 500));
    }

    private function resetPassword($request) {

        $userData = User::whereEmail($request->email)->first();
        $userData->update([
            'password'=>bcrypt($request->password)
        ]);
        $this->updatePasswordRow($request)->delete();

        return response()->json(LibraryController::responseApi([], 'A senha foi atualizada.'));

      }
}
