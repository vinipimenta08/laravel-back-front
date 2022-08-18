<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\Menurole;
use App\Models\Menus;
use App\Models\UserMenus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($data = null)
    {
        try {
            $user = auth('api')->user();
            $menus = new Menus;
            if ($user->alternative_profile) {
                $userMenus = UserMenus::where('id_user', $user->id)->select('id_menu');
                $menus = $menus->whereIn('id', $userMenus);
            } else {
                $menuroles = explode(',',$user->menuroles);
                $menus = $menus->with('roles')->join('menu_role', 'menu_role.menus_id', '=', 'menus.id')
                        ->whereIn('role_name', $menuroles)->select('menus.*')->distinct();
            }
            $menus = $menus->orderBy('menus.sequence');
            $menus = $menus->get();
            if ($data) {
                return $menus;
            } 
            return response()->json(LibraryController::responseApi($menus));
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
            $menu = new Menus;
            $menu->fill($request->all());
            $menu->save();
            $lastId = DB::getPdo()->lastInsertId();
            foreach ($request->all()['roles'] as $key => $value) {
                $menurole = new Menurole;
                $menurole->role_name = $value;
                $menurole->menus_id = $lastId;
                $menurole->save();
            }        
            return response()->json(LibraryController::responseApi($menu, 'ok'));
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
            $user = auth('api')->user();
            $menuroles = explode(',',$user->menuroles);
            $menus = Menus::join('menu_role', 'menu_role.menus_id', '=', 'menus.id')
                    ->where('menus.id',$id)
                    ->whereIn('role_name', $menuroles)
                    ->select('menus.*')->with('roles')
                    ->distinct()
                    ->get();
            return response()->json(LibraryController::responseApi($menus));
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
            $library = new LibraryController; 
            $menu = Menus::findOrFail($id);
            $menu->fill($request->all());
            $library->logupdate($menu);
            $menu->update();
            Menurole::where('menus_id', $id)->delete();
            foreach ($request->all()['roles'] as $key => $value) {
                $menurole = new Menurole;
                $menurole->role_name = $value;
                $menurole->menus_id = $id;
                $menurole->save();
            }
            return response()->json(LibraryController::responseApi($menurole, 'ok'));
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
            $menu = Menus::findOrFail($id);
            $menu->delete();
            Menurole::where('menus_id', $id)->delete();
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

    public function active(Request $request)
    {
        try {
            $menus = Menus::where('id', $request->id)->first();
            $menus->active = $request->active;
            LibraryController::logupdate($menus);
            $menus->update();
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function aplication()
    {
        try {
            $user = auth('api')->user();
            $menus = new Menus;
            if ($user->alternative_profile) {
                $userMenus = UserMenus::where('id_user', $user->id)->select('id_menu');
                $menus = $menus->whereIn('id', $userMenus);
            } else {
                $menuroles = explode(',',$user->menuroles);
                $menus = $menus->with('roles')->join('menu_role', 'menu_role.menus_id', '=', 'menus.id')
                        ->whereIn('role_name', $menuroles)->select('menus.*')->distinct();
            }
            $menus = $menus->where('active', 1)->orderBy('menus.sequence');
            $menu = $menus->get();

            $users = new UserController;
            $campaigns = new CampaignsController;
            $client = $users->clients('data');
            $campaign = $campaigns->index('data');
            $user = ['name' => auth('api')->user()['name']];
            return response()->json(LibraryController::responseApi(['menu' => $menu, 'client' => $client, 'campaign' => $campaign, 'user' => $user]));
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
}
