<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Mail\SendEmailRecoverPassword;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use stdClass;

use function Psy\debug;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('password.index');
    }

    public function passwordRequest(Request $request)
    {
        $resetpasswordrequest = [];
        try {
            $email = $request->email;

            $libraryController = new LibraryController;

            $head = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            $resetpasswordrequest = $libraryController->requestAsync("POST", "/api/resetpasswordrequest", ["email" => $email], $head);
            $token = $resetpasswordrequest['data']['token'];

            $changepassword = $libraryController->requestAsync("GET", "/api/changepassword", ["token" => $token], $head);

            $email = (String) $changepassword['data'][0]['email'];

            $user = new stdClass();
            $user->email = $email;
            $user->link = config('app.url').'/password?token='.$token ;

            Mail::send(new SendEmailRecoverPassword($user));

            return $resetpasswordrequest;
        } catch (Exception $e) {
            Log::debug($e);
            return $resetpasswordrequest;
        }

    }

    public function passwordResetProcess(Request $request){

        $changepassword = [];
        try {
            $libraryController = new LibraryController;

            $head = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            $changepassword = $libraryController->requestAsync("GET", "/api/changepassword", ["token" => $request->token], $head);
            $email = (String) $changepassword['data'][0]['email'];

            $user = new stdClass();
            $user->email = $email;
            $user->token = $request->token;
            $user->password = $request->password;

            $changepassword = $libraryController->requestAsync("POST", "/api/changepassword", $user, $head);
            Session::put('success', 'Senha alterada!');
            return $changepassword;
        } catch (Exception $e) {
            Log::debug($e);
            return $changepassword;
        }
    }
}
