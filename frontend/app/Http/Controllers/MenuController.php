<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $libraryController = new LibraryController;
            $menus = $libraryController->requestAsync("GET", "/api/menus");
            $profiles = $libraryController->requestAsync('GET', '/api/profile');
    
            $menusData = $menus['data'];
            return view('menus.index', ['menus' => $menusData, 'profiles' => $profiles['data']]);
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
            $libraryController = new LibraryController;
            $rules = [
                'name' => 'required',
                'slug' => 'required',
                'sequence' => 'required'
            ];
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $response = $libraryController->requestAsync("POST", "/api/menus", $request->all());
            return $response;
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
            $menu = $libraryController->requestAsync("GET", "/api/menus/$id");
            $profiles = $libraryController->requestAsync('GET', '/api/profile');
            $rules = [];
            foreach ($menu['data'][0]['roles'] as $key => $value) {
                $rules[] = $value['role_name'];
            }
            return view('menus.edit', ['menu' => $menu['data'][0], 'profiles' => $profiles['data'], 'rules' => $rules]);
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
            $libraryController = new LibraryController;
            $rules = [
                'name' => 'required',
                'slug' => 'required',
                'sequence' => 'required'
            ];
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $response = $libraryController->requestAsync("PUT", "/api/menus/$id", $request->all());
            return $response;
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleUpdateError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function atualizar(Request $request, $id)
    {
        try {
            $libraryController = new LibraryController;
            $rules = [
                'name' => 'required',
                'slug' => 'required',
                'sequence' => 'required'
            ];
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $response = $libraryController->requestAsync("PUT", "/api/menus/$id", $request->all());
    
            return $response;
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
            $libraryController = new LibraryController;
            $response = $libraryController->requestAsync("DELETE", "/api/menus/$id");
            return response($response);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDestroyError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function active(Request $request)
    {
        try {
            $data = [
                'id' => $request->id,
                'active' => $request->active == 'true' ? 1 : 0
            ];
            $libraryController = new LibraryController;
            $response = $libraryController->requestAsync("PUT", "/api/menus/active", $data);
            return response($response);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleActiveError', ["status" => ($request->active == 'true' ? 'ativar' : 'inativar')]), "message" => __('messages.defaultMessage')], "", 500);
        }
    }
}
