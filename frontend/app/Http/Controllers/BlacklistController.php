<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class BlacklistController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!$request->request_server) {
                return redirect(route('layout'));
            }
            $libraryController = new LibraryController;
            $blacklists = $libraryController->requestAsync('GET', '/api/blacklists');

            if ($blacklists['error'] != 0) {
                return view('home');
            }

            return view('blacklist.index', ['blacklists' => $blacklists['data']]);

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function advancedSearch(Request $request)
    {
        try {
            $check_filtrar = $request->check_filtrar;
            $phone = $request->phone;

            $dddPhone = preg_replace('/[^0-9]/', '', $phone);

            $ddd = substr($dddPhone, 0, 2);
            $num = substr($dddPhone, 2, 9);

            $request->request->add([
                'ddd' => $ddd,
                'phone' => $num
            ]);

            $libraryController = new LibraryController;
            $blacklists = $libraryController->requestAsync('POST', '/api/blacklists/advancedSearch', $request->all());

            if ($check_filtrar == "true") {
                return view('blacklist.filtro', ['blacklists' => $blacklists['data']]);
            }else{
                return view('blacklist.table', ['blacklists' => $blacklists['data']]);
            }

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleLoadPageError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function downloadlayout()
    {
        return view('blacklist.downloadlayout');
    }

    public function destroy($id)
    {
        try {
            $libraryController = new LibraryController;
            $blacklist = $libraryController->requestAsync("DELETE", "/api/blacklists/$id");
            return response($blacklist);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDestroyError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function destroyLote(Request $request)
    {
        try {
            $libraryController = new LibraryController;
            $blacklist = $libraryController->requestAsync("POST", "/api/blacklists/destroyLote", $request->all());
            return response($blacklist);
        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDestroyError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function importList(Request $request)
    {

        try{
            $libraryController = new LibraryController;
            $clients = $libraryController->requestAsync('GET', '/api/clients');
            foreach ($clients['data'] as $row) {
                $id_client = $row['id'];
                // Handle File Upload
                if($request->hasFile('file')){
                    // dd("teste");
                    // Get filename with the extension
                    $filenameWithExt = $request->file('file')->getClientOriginalName();
                    // Get just filename
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    // Get just ext
                    $extension = $request->file('file')->getClientOriginalExtension();
                    // Filename to store
                    $fileNameToStore= "genion_blacklist_".$id_client."_".time().'.'.$extension;

                    // Upload File
                    $path = $request->file('file')->storeAs('upload', $fileNameToStore);

                    $file_name_original = $filename.'.'.$extension;
                    $file_name_genion =$fileNameToStore;
                    $id_client = $id_client;
                    $libraryController = new LibraryController;

                    $ResLoadFile = [
                            'file_name_original' => $file_name_original,
                            'file_name_genion' => $file_name_genion,
                            'id_client' => $id_client
                    ];
                    $response = $libraryController->requestAsync('POST', '/api/blacklists/upload', $ResLoadFile);

                    return $libraryController->responseApi(['file_name_genion' => $file_name_genion]);

                } else {
                    return LibraryController::responseApi(["title" => __('Erro arquivo'), "message" => __('Não existe na pasta')], "", 500);
                }
            }

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleValidateFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

    public function downloadFile(Request $request)
    {
        try {

            $library = new LibraryController;
            $returnBlacklist = $library->requestAsync("GET", "/api/blacklists/downloadFile", $request->all());

            $count=0;
            $reportlist = array();
            foreach ($returnBlacklist['data'] as $key => $row) {
                $mailing_file_original = $row['mailing_file_original'];
                $reportlist['data'][$count]['celular'] = $row['ddd'].$row['phone'];
                $count++;
            }

            $arrayLines[] = '"CELULAR"';

            foreach ($reportlist['data'] as $key => $value) {
                $arrayLines[] = implode(';', $value);
            }

            if (count($arrayLines) > 0) {

                $csv = implode("\n", $arrayLines);
                $name = $mailing_file_original;

                return ['content' => $csv, 'name' => $name];
            }

            return ['erro' => 1, 'title' => "Vazio!", 'message' => "Nenhum dado encontrado."];

        } catch (Exception $e) {
            LibraryController::recordError($e);
            return LibraryController::responseApi(["title" => __('messages.titleDownloadFileError'), "message" => __('messages.defaultMessage')], "", 500);
        }
    }

}
