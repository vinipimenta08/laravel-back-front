<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\TokenController;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use \GuzzleHttp\Psr7\Request as RequestGuzzle;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function autentication(Request $request)
    {
        
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $head = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        $bodyRequest = $request->only(['email', 'password']);
        $body = json_encode($bodyRequest);
        
        $credentials = [];
        try {
            $client = new Client();
            $requestUser = new RequestGuzzle('POST', env('END_POINT_BACKEND') . '/api/login/user', $head, $body);
            $promiseCredentials = $client->sendAsync($requestUser)->then(function ($response) {
                return json_decode($response->getBody()->getContents(),true);
            });
            $credentials = $promiseCredentials->wait();

        } catch (Exception $e) {
            Session::put('fail', 'Email ou senha incorreto');
            return view('auth.login', ['credential' => $credentials]);
        }
        $tokenController = new TokenController;
        $tokenController->setCredentials($credentials);
        $expires_in = Carbon::now()->addSeconds(($credentials['expires_in'] - env('SESSION_EXPIRE')))->format('Y-m-d H:i:s');
        Session::put('expires_in', $expires_in);
        return redirect(route('layout'));
    }

    public static function logout()
    {

        LibraryController::requestAsync('POST', '/api/logout');
        Session::forget('credentials');
        Session::forget('nameUser');
        Session::forget('last_activity');
        return redirect(route('login'));
    }

    public static function refresh()
    {
        $libraryController = new LibraryController;
        $credentials = $libraryController->requestAsync('POST', '/api/refresh');
        
        Session::forget('credentials');
        Session::forget('nameUser');
        if (isset($credentials['error'])) {
            Session::put('fail', 'Email ou senha incorreto');
            return view('auth.login', ['credential' => $credentials]);
        }
        $expires_in = Carbon::now()->addSeconds(($credentials['expires_in'] - env('SESSION_EXPIRE')))->format('Y-m-d H:i:s');
        Session::put('credentials', $credentials);
        Session::put('expires_in', $expires_in);
    }
}
