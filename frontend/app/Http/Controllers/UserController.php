<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Request as RequestGuzzle;

class UserController extends Controller
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
            $users = $libraryController->requestAsync('GET', '/api/users');
            $Currentuser = $libraryController->requestAsync('POST', '/api/me');
            $clients = $libraryController->requestAsync('GET', '/api/clients');
            $profiles = $libraryController->requestAsync('GET', '/api/profile');
            $menus = $libraryController->requestAsync('GET', '/api/menus');
            $active = 0;
            $inactive = 0;
            foreach ($users['data'] as $key => $user) {
                if ($user['active']) {
                    $active ++;
                } else {
                    $inactive ++;
                }
            }
            $initActive = '';
            if ($active >= $inactive) {
                $initActive = 'checked';
            }
            return view('user.index',['users' => $users['data'], 'clients' => $clients['data'], 'profiles' => $profiles['data'], 'menus' => $menus['data'], 'initActive' => $initActive, 'Currentuser' => $Currentuser]);
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
                'email' => 'required|email',
                'password' => 'required|same:confirm_password',
                'confirm_password' => 'required'
            ];
            if (!isset($request->alternative_profile)) {
                $rules['id_client'] = 'required';
                $rules['id_profile'] = 'required';
                $request->request->add(['alternative_profile' => 0]);
            }else {
                $rules['clients'] = 'required';
                $rules['menus'] = 'required';
            }
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $menuroles = 'user';
            if ($request->id_profile == 1) {
                $menuroles = 'root,admin,user';
            }else if ($request->id_profile == 2) {
                $menuroles = 'admin,user';
            }else if ($request->id_profile == 3) {
                $menuroles = 'user';
            }
            $request->request->add(['menuroles' => $menuroles]);
            $request->request->add(['active' => 1]);
    
            $libraryController = new LibraryController;
            $responseUser = $libraryController->requestAsync('POST', '/api/users', $request->all());
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
            $userClients = [];
            $userMenus = [];
            $userResposne = $libraryController->requestAsync("GET", "/api/users/$id");
            $profiles = $libraryController->requestAsync("GET", "/api/profile");
            $menus = $libraryController->requestAsync("GET", "/api/menus");
            $clients = $libraryController->requestAsync("GET", "/api/clients");
            $user = $userResposne['data']['user'];
            foreach ($userResposne['data']['clients'] as $key => $value) {
                $userClients[] = $value['id'];
            }
            foreach ($userResposne['data']['menus'] as $key => $value) {
                $userMenus[] = $value['id'];
            }
            return view('user.edit', ['user' => $user, 'clients' => $clients['data'], 'profiles' => $profiles['data'], 'menus' => $menus['data'], 'userClients' => $userClients, 'userMenus' => $userMenus]);
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
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'required_with:password'
            ];
            if (!isset($request->alternative_profile)) {
                $rules['id_client'] = 'required';
                $rules['id_profile'] = 'required';
                $request->request->add(['alternative_profile' => 0]);
            }else {
                $rules['clients'] = 'required';
                $rules['menus'] = 'required';
            }
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $menuroles = 'user';
            if ($request->id_profile == 1) {
                $menuroles = 'root,admin,user';
            }else if ($request->id_profile == 2) {
                $menuroles = 'admin,user';
            }else if ($request->id_profile == 3) {
                $menuroles = 'user';
            }
            $request->request->add(['menuroles' => $menuroles]);

            $libraryController = new LibraryController;
            $responseUser = $libraryController->requestAsync('PUT', "/api/users/$id", $request->except($except));
            return response($responseUser);
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
            0/0;
            $libraryController = new LibraryController;
            $user = $libraryController->requestAsync("DELETE", "/api/users/$id");
            return response($user);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDestroyError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function active_profile(Request $request)
    {
        try {
            $data = [
                'id' => $request->id,
                'active' => $request->active == 'true' ? 1 : 0
            ];
            $libraryController = new LibraryController;
            $response = $libraryController->requestAsync("PUT", "/api/users/activeprofile", $data);

            return response($response);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleActiveError', ["status" => ($request->active == 'true' ? 'ativar' : 'inativar')]), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function active_all(Request $request)
    {
        try {
            $data = [
                'active' => $request->active == 'true' ? 1 : 0
            ];
            $libraryController = new LibraryController;
            $response = $libraryController->requestAsync("PUT", "/api/users/activeall", $data);

            return response($response);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleActiveError', ["status" => ($request->active == 'true' ? 'ativar' : 'inativar')]), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function roles($request, $user = null)
    {
        switch ($request->method()) {
            case 'POST':
                    $rules['email'] = 'required|email|unique:users';
                    $rules['password'] = 'required';
                break;
            case 'PUT':
                    $rules['email'] = [
                                        'required',
                                        'email',
                                        Rule::unique('users')->ignore($user->id),
                                    ];
                break;
            default:
                break;
        }
        return $rules;
    }
}
