<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\Clients;
use App\Models\Menus;
use App\Models\UserClient;
use App\Models\UserMenus;
use App\Models\Users;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $me = auth('api')->user();

            $users = new Users;
            if ($me->alternative_profile) {
                $clientsId = UserClient::where('id_user', $me->id)->select('id_client')->get()->toArray();
                $users = $users->whereIn('id_client',$clientsId);
            }else {
                if ($me->id_profile != 1) {
                    $users = $users->where('id_client',$me->id_client)->where('id_profile', '<>', 1);
                }
            }
            $users = $users->get();
            return response()->json(LibraryController::responseApi($users));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function clients($data = null)
    {
        try {
            $user = auth('api')->user();
            $clients = new Clients;
            if ($user->id_profile != 1) {
                if ($user->alternative_profile) {
                    $clientsId = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $clients = $clients->whereIn('id',$clientsId);
                }else {
                    $clients = $clients->where('id',$user->id_client);
                }
            }
            $clients = $clients->get();
            if ($data) {
                return $clients;
            }
            return response()->json(LibraryController::responseApi($clients));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
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
            $validator = Validator::make($request->all(), $this->roles($request));
            if ($validator->fails()) {
                return response()->json(LibraryController::responseApi([], $validator->getMessageBag(), 100));
            }
            if ($request->alternative_profile) {
                $user = new Users();
                $user->fill($request->all());
                if ($request->alternative_profile) {
                    if (count($request->clients) == 1) {
                        $user->id_client = $request->clients[0];
                    }
                }
                $user->password = bcrypt($user->password);
                $user->save();
                $lastId = DB::getPdo()->lastInsertId();
                
                foreach ($request->menus as $value) {
                    $menu = Menus::find($value);
                    if ($menu) {
                        UserMenus::create([
                            'id_user' => $lastId,
                            'id_menu' => $menu->id
                        ]);
                    }
                }
                foreach ($request->clients as $value) {
                    $client = Clients::find($value);
                    if ($client) {
                        UserClient::create([
                            'id_user' => $lastId,
                            'id_client' => $client->id
                        ]);
                    }
                }
            }else{
                $user = new Users();
                $user->fill($request->all());
                $user->password = bcrypt($user->password);
                $user->save();
            }
            return response()->json(LibraryController::responseApi($user, 'ok'));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
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
            $user = Users::find($id);
            $clients = new Clients;
            $menus = new Menus;
                if ($user->alternative_profile) {
                    $clientsId = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                    $menusId = UserMenus::where('id_user', $user->id)->select('id_menu')->get()->toArray();
                    $clients = $clients->whereIn('id',$clientsId)->get()->toArray();
                    $menus = $menus->whereIn('id',$menusId)->get()->toArray();
                }else {
                    $clients = [];
                    $menus = [];
                }
            return response()->json(LibraryController::responseApi(['user' => $user, 'clients' => $clients, 'menus' => $menus]));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
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
            $user = Users::findOrFail($id);
            $user->fill($request->all());
            if ($request->alternative_profile) {
                if (count($request->clients) == 1) {
                    $user->id_client = $request->clients[0];
                }
            }
            if (!$request->has('password') || $request->password == "") {
                unset($request->all()['password']);
                unset($user->password);
            }else {
                $user->password = bcrypt($request->password);
            }
            $validator = Validator::make($request->all(), $this->roles($request, $user));
            if ($validator->fails()) {
                return response()->json(LibraryController::responseApi([], $validator->getMessageBag(), 100));
            }
            if ($request->alternative_profile) {
                LibraryController::logupdate($user);
                $user->update();                 
                UserMenus::where('id_user', $id)->delete();
                UserClient::where('id_user', $id)->delete();
                foreach ($request->menus as $value) {
                    $menu = Menus::find($value);
                    if ($menu) {
                        UserMenus::create([
                            'id_user' => $id,
                            'id_menu' => $menu->id
                        ]);
                    }
                }
                foreach ($request->clients as $value) {
                    $client = Clients::find($value);
                    if ($client) {
                        UserClient::create([
                            'id_user' => $id,
                            'id_client' => $client->id
                        ]);
                    }
                }
                
            }else{
                LibraryController::logupdate($user);
                $user->update();
            }

            return response()->json(LibraryController::responseApi($user, 'ok'));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
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
            $user = Users::findOrFail($id);
            if ($user->alternative_profile) {
                UserMenus::where('id_user', $id)->delete();
                UserClient::where('id_user', $id)->delete();
            }
            $user->delete();
            return response()->json(LibraryController::responseApi($user, 'ok'));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    public function active_profile(Request $request)
    {
        try {
            $user = Users::findOrFail($request->id);
            $user->active = $request->active;
            $user->enable_all = $request->active;
            LibraryController::logupdate($user);
            $user->update();
            return response()->json(LibraryController::responseApi([], 'ok'));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
        }
    }

    public function active_all(Request $request)
    {
        try {
            $currentUser = auth('api')->user();

            $users = new Users;
            if ($currentUser->alternative_profile) {
                $clientsId = UserClient::where('id_user', $currentUser->id)->select('id_client')->get()->toArray();
                $users = $users->whereIn('id_client',$clientsId);
            }else {
                if ($currentUser->id_profile != 1) {
                    $users = $users->where('id_client',$currentUser->id_client);
                }
            }
            $users = $users->where('id_profile', "<>", 1)
                            ->where('enable_all', 1)
                            ->get();

            foreach ($users as $key => $user) {
                $user->active = $request->active;
                LibraryController::logupdate($user);
                $user->update();
            }
            return response()->json(LibraryController::responseApi([], 'ok'));
        } catch (Exception $e) {
            LibraryController::recordError($e);
            if ($e->getCode()) {
                $code = $e->getCode();
            }else {
                $code = 500;
            }
            return response()->json(LibraryController::responseApi([],$e->getMessage(), $code));
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
