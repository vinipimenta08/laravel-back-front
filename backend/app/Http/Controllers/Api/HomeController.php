<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LibraryController;
use App\Models\HistListCustom;
use App\Models\UserClient;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashdata(Request $request)
    {
        $dataAtual = date("Y-m-d");

        if (($request->date_init == $dataAtual) && ($request->date_end == $dataAtual)) {

            $authController = new AuthController;
            $user_acount = $authController->me($request);
            $user = auth('api')->user();

            $hist = new HistListCustom;

            if ($user->alternative_profile) {
                $userClient = UserClient::where('id_user', $user->id)->select('id_client')->get()->toArray();
                $hist = $hist->whereIn('id_client', $userClient);
            }else {
                if ($user->id_profile != 1) {
                    $hist = $hist->where('id_client', $user->id_client);
                }
            }

            $hist = $hist->get();

            $lastDays = array();
            $location = array();
            foreach ($hist as $row) {
                $card[$row['id']]['id'] = $row['id'];
                $card[$row['id']]['sended'] = $row['sended'];
                $card[$row['id']]['opening'] = $row['opening'];
                $card[$row['id']]['reply'] = $row['reply'];

                $listCustom[$row['id']]['id_client'] = $row['id_client'];
                $listCustom[$row['id']]['name_client'] = $row['name_client'];
                $listCustom[$row['id']]['base'] = $row['base'];
                $listCustom[$row['id']]['imported'] = $row['imported'];
                $listCustom[$row['id']]['failed'] = $row['failed'];
                $listCustom[$row['id']]['sended'] = $row['sended'];
                $listCustom[$row['id']]['opening'] = $row['opening'];
                $listCustom[$row['id']]['reply'] = $row['reply'];
                $listCustom[$row['id']]['sended_at'] = $row['sended_at'];
                $listCustom[$row['id']]['campaign']['id'] = $row['id_campaign'];
                $listCustom[$row['id']]['campaign']['name'] = $row['name_campaign'];

                $days = explode("|", $row['last_days']);
                foreach ($days as $value) {
                    $dados = explode("_", $value);
                    @$lastDays[$dados[0]]['total'] += $dados[1];
                    @$lastDays[$dados[0]]['date_received'] = $dados[0];
                }

                $states = explode("|", $row['location']);
                foreach ($states as $value) {
                    $dados = explode("_", $value);
                    @$location[$dados[0]]['total'] += $dados[1];
                    @$location[$dados[0]]['estado'] = $dados[0];
                }

            }

            ksort($lastDays);
            arsort($location);

            unset($lastDays[""]);

            $greateropening = array_slice($location, 0, 4);

            $total = 0;
            foreach ($hist as $row) {
                if ($user->id_profile == 1) {
                    $total += $row->total;
                }else{
                    if ($user->id_client == $row->id_client) {
                        $total += $row->total;
                    }
                }
            }

            foreach ($greateropening as $key => $data) {
                $greateropening[$key]['percent'] = ($total == 0) ? 0 : round(((100 * $data['total']) / $total),2);
            }

            unset($greateropening[""]);

            $card = (isset($card) != '')?$card:[];
            $listCustom = (isset($listCustom) != '')?$listCustom:[];
            $lastDays = (isset($lastDays) != '')?$lastDays:[];
            $greateropening = (isset($greateropening) != '')?$greateropening:[];

            $card = LibraryController::responseApi($card);
            $listCustom = LibraryController::responseApi($listCustom);
            $lastDays = LibraryController::responseApi($lastDays);
            $greateropening = LibraryController::responseApi($greateropening);

            return LibraryController::responseApi([
                                                    'card' => $card,
                                                    'listCustom' => $listCustom,
                                                    'userAcount' => $user_acount->original,
                                                    'lastDays' => $lastDays,
                                                    'greateropening' => $greateropening
            ]);

        }else{
            $smsReportController = new smsReportController();
            $listCustomController = new ListCustomController;
            $authController = new AuthController;
            $card = $smsReportController->openandreplysended($request);
            $listCustom = $listCustomController->statuscustom($request);
            $user_acount = $authController->me($request);
            $lastDays = $smsReportController->lastdays($request);
            $greateropening = $listCustomController->greateropening($request);
            return LibraryController::responseApi([
                                                    'card' => $card->original,
                                                    'listCustom' => $listCustom->original,
                                                    'userAcount' => $user_acount->original,
                                                    'lastDays' => $lastDays->original,
                                                    'greateropening' => $greateropening->original
                                                ]);
        }

        $smsReportController = new smsReportController;
        $listCustomController = new ListCustomController;
        $authController = new AuthController;
        $card = $smsReportController->openandreplysended($request);
        $listCustom = $listCustomController->statuscustom($request);
        $user_acount = $authController->me($request);
        $lastDays = $smsReportController->lastdays($request);
        $greateropening = $listCustomController->greateropening($request);
        return LibraryController::responseApi([
                                                'card' => $card->original,
                                                'listCustom' => $listCustom->original,
                                                'userAcount' => $user_acount->original,
                                                'lastDays' => $lastDays->original,
                                                'greateropening' => $greateropening->original
                                            ]);
    }
}
