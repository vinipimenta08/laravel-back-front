<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerBillingController extends Controller
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
            $customer = $libraryController->requestAsync('GET', '/api/customerbilling');
            $clients = $libraryController->requestAsync('GET', '/api/clients');
            return view('customer_billing.index', ['customer' => $customer['data'], 'clients' => $clients['data']]);
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
                'id_client' => 'required',
                'value' => 'required'
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $libraryController = new LibraryController;
            $returnCustomer = $libraryController->requestAsync('POST', '/api/customerbilling', $request->all());
            return $returnCustomer;
        }  catch (Exception $e) {
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
            $customer = $libraryController->requestAsync("GET", "/api/customerbilling/$id");
            $clients = $libraryController->requestAsync('GET', '/api/clients');
            return view('customer_billing.edit', ['custome' => $customer['data'], 'clients' => $clients['data']]);
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
            $rules = [
                'id_client' => 'required',
                'value' => 'required'
            ];
            $validate = Validator::make($request->all(), $rules);

            if ($validate->fails()) {
                return response()->json(LibraryController::responseApi([], $validate->getMessageBag(), 100));
            }
            $libraryController = new LibraryController;
            $customer = $libraryController->requestAsync("PUT", "/api/customerbilling/$id", $request->all());
            return $customer;
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
            $customer = $libraryController->requestAsync("DELETE", "/api/customerbilling/$id");
            return $customer;
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDestroyError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

}
