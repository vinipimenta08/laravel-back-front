<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthControllerClient;
use App\Http\Controllers\Api\BlacklistController;
use App\Http\Controllers\Api\CalendarsController;
use App\Http\Controllers\Api\CampaignsClientController;
use App\Http\Controllers\Api\CampaignsController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ListCustomClientController;
use App\Http\Controllers\Api\ListCustomController;
use App\Http\Controllers\Api\LogSystemController;
use App\Http\Controllers\Api\MenusController;
use App\Http\Controllers\Api\ProfilesController;
use App\Http\Controllers\Api\smsController;
use App\Http\Controllers\Api\smsReportController;
use App\Http\Controllers\Api\StatusLinkController;
use App\Http\Controllers\Api\StatusSendKolmeyaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ValueFireController;
use App\Http\Controllers\Api\BrokerSmsController;
use App\Http\Controllers\Api\ChangePasswordController;
use App\Http\Controllers\Api\CustomerBillingController;
use App\Http\Controllers\Api\PasswordResetRequestController;
use App\Http\Controllers\Api\HistListCustomController;
use App\Http\Controllers\Api\SmsSendingProgramController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Users
Route::post('login/user', [AuthController::class, 'login']);
Route::group(['middleware' => ['apiJwt']], function(){
    // AuthController
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    // MenusController
    Route::get('menus', [MenusController::class, 'index']);
    Route::post('menus', [MenusController::class, 'store']);
    Route::get('menus/aplication', [MenusController::class, 'aplication']);
    Route::post('menus', [MenusController::class, 'store']);
    Route::put('menus/active', [MenusController::class, 'active']);
    Route::get('menus/{id}', [MenusController::class, 'show']);
    Route::put('menus/{id}', [MenusController::class, 'update']);
    Route::delete('menus/{id}', [MenusController::class, 'destroy']);
    // UserController
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::put('users/activeprofile', [UserController::class, 'active_profile']);
    Route::put('users/activeall', [UserController::class, 'active_all']);
    Route::get('users/client', [UserController::class, 'clients']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    // CampaignsController
    Route::get('campaigns', [CampaignsController::class, 'index']);
    Route::post('campaigns', [CampaignsController::class, 'store']);
    Route::put('campaigns/activecostcenter', [CampaignsController::class, 'active_cost_center']);
    Route::get('campaigns/statuscampaigns', [CampaignsController::class, 'statuscampaign']);
    Route::get('campaigns/reportlist', [CampaignsController::class, 'reportlist']);
    Route::get('campaigns/reporterrors', [CampaignsController::class, 'reporterrors']);
    Route::get('campaigns/reportreply', [CampaignsController::class, 'reportreply']);
    Route::get('campaigns/report', [CampaignsController::class, 'report']);
    Route::get('campaigns/{id}', [CampaignsController::class, 'show']);
    Route::put('campaigns/{id}', [CampaignsController::class, 'update']);
    Route::delete('campaigns/{id}', [CampaignsController::class, 'destroy']);
    // ListCustomController
    Route::post('listcustom/uploadcustom', [ListCustomController::class, 'uploadfilecustom']);
    Route::post('listcustom/validateListCustom', [ListCustomController::class, 'validateListCustom']);
    Route::get('listcustom/greateropening', [ListCustomController::class, 'greateropening']);
    Route::get('listcustom/status', [ListCustomController::class, 'statuscustom']);
    Route::get('listcustom/status/{id_campaign}', [ListCustomController::class, 'statuscustomspecific']);
    Route::post('listcustom/loadfile', [ListCustomController::class, 'LoadFile']);
    Route::post('listcustom/validateLoadFile', [ListCustomController::class, 'validateLoadFile']);
    Route::post('listcustom/deleteloadfile', [ListCustomController::class, 'deleteLoadFile']);
    Route::post('listcustom/uploadloadfile', [ListCustomController::class, 'uploadLoadFile']);
    Route::post('listcustom/programLoadFile', [ListCustomController::class, 'programLoadFile']);
    Route::post('listcustom/validClientJustSMS/{id_client}', [ListCustomController::class, 'validClientJustSMS']);
    // ClientController
    Route::get('clients', [ClientController::class, 'index']);
    Route::post('clients', [ClientController::class, 'store']);
    Route::put('clients/active', [ClientController::class, 'active']);
    Route::get('clients/valuesended', [ClientController::class, 'valuesended']);
    Route::get('clients/{id}', [ClientController::class, 'show']);
    Route::put('clients/{id}', [ClientController::class, 'update']);
    Route::delete('clients/{id}', [ClientController::class, 'destroy']);
    // ValueFireController
    Route::get('valuefire',[ValueFireController::class, 'index']);
    Route::post('valuefire',[ValueFireController::class, 'store']);
    Route::get('valuefire/{id}',[ValueFireController::class, 'show']);
    Route::put('valuefire/{id}',[ValueFireController::class, 'update']);
    Route::delete('valuefire/{id}',[ValueFireController::class, 'destroy']);
    // StatusSendKolmeyaController
    Route::get('statuskolmeya', [StatusSendKolmeyaController::class, 'index']);
    Route::post('statuskolmeya', [StatusSendKolmeyaController::class, 'store']);
    Route::get('statuskolmeya/{id}', [StatusSendKolmeyaController::class, 'show']);
    Route::put('statuskolmeya/{id}', [StatusSendKolmeyaController::class, 'update']);
    Route::delete('statuskolmeya/{id}', [StatusSendKolmeyaController::class, 'destroy']);
    // StatusLinkController
    Route::get('statuslink', [StatusLinkController::class, 'index']);
    Route::post('statuslink', [StatusLinkController::class, 'store']);
    Route::get('statuslink/{id}', [StatusLinkController::class, 'show']);
    Route::put('statuslink/{id}', [StatusLinkController::class, 'update']);
    Route::delete('statuslink/{id}', [StatusLinkController::class, 'destroy']);
    // smsReportController
    Route::get('sms/responsekolmeyasms', [smsReportController::class, 'responseKolmeyaSms']);
    Route::post('sms/resend', [smsReportController::class, 'sendbystatusendcampaign']);
    Route::get('sms/openandreplysended', [smsReportController::class, 'openandreplysended']);
    Route::get('sms/lastdays', [smsReportController::class, 'lastdays']);
    // SmsController
    Route::get('sms/responseKolmeya', [smsController::class, 'responseKolmeya'])->name('responseKolmeya');
    Route::get('sms/responseSmsTalkip/{id}', [smsController::class, 'responseSmsTalkip'])->name('responseSMSTalkip');
    Route::get('sms/statuskolmeya', [smsController::class, 'statussmskolmeya']);
    Route::get('sms/statusSmsTalkip/{type?}', [smsController::class, 'statusSmsTalkip']);
    Route::post('sms/sendonesms/{id?}', [smsController::class, 'sendonesms']);
    Route::post('sms/sendmultiplesms/{id?}', [smsController::class, 'sendmultiplesms']);
    // ProfilesController
    Route::get('profile', [ProfilesController::class, 'index']);
    // HomeController
    Route::get('home/dashdata', [HomeController::class, 'dashdata']);
    // LogSystemController
    Route::post('logsystem/lognavegation', [LogSystemController::class, 'logNavigation']);
    Route::post('logsystem/recorderror', [LogSystemController::class, 'recorderror']);
    // BrokerSmsController
    Route::get('brokersms', [BrokerSmsController::class, 'index']);
    Route::post('brokersms', [BrokerSmsController::class, 'store']);
    Route::put('brokersms/active', [BrokerSmsController::class, 'active']);
    Route::get('brokersms/{id}', [BrokerSmsController::class, 'show']);
    Route::put('brokersms/{id}', [BrokerSmsController::class, 'update']);
    Route::delete('brokersms/{id}', [BrokerSmsController::class, 'destroy']);
    // BlacklistController
    Route::get('blacklists', [BlacklistController::class, 'index']);
    Route::get('blacklists/downloadFile', [BlacklistController::class, 'downloadFile']);
    Route::delete('blacklists/{id}', [BlacklistController::class, 'destroy']);
    Route::post('blacklists/upload', [BlacklistController::class, 'upload']);
    Route::post('blacklists/advancedSearch', [BlacklistController::class, 'advancedSearch']);
    Route::post('blacklists/destroyLote', [BlacklistController::class, 'destroyLote']);
    // CustomerBillingController
    Route::get('customerbilling', [CustomerBillingController::class, 'index']);
    Route::post('customerbilling', [CustomerBillingController::class, 'store']);
    Route::get('customerbilling/{id}', [CustomerBillingController::class, 'show']);
    Route::put('customerbilling/{id}', [CustomerBillingController::class, 'update']);
    Route::delete('customerbilling/{id}', [CustomerBillingController::class, 'destroy']);
    // SmsSendingProgramController
    Route::get('smsprogram', [SmsSendingProgramController::class, 'index']);
    Route::post('smsprogram', [SmsSendingProgramController::class, 'store']);
    Route::get('smsprogram/{id}', [SmsSendingProgramController::class, 'show']);
    Route::put('smsprogram/{id}', [SmsSendingProgramController::class, 'update']);
    Route::delete('smsprogram/{id}', [SmsSendingProgramController::class, 'destroy']);
    Route::post('smsprogram/active', [SmsSendingProgramController::class, 'active']);
    Route::post('smsprogram/sendSmsProgrammed', [SmsSendingProgramController::class, 'sendSmsProgrammed']);
    Route::post('smsprogram/updateSmsProgrammed', [SmsSendingProgramController::class, 'updateSmsProgrammed']);
});

// Client
Route::group(['prefix' => 'client'], function(){
    Route::post('login', [AuthControllerClient::class, 'login']);

    Route::group(['middleware' => ['apiJwtClient']], function(){
        // AuthControllerClient
        Route::post('logout', [AuthControllerClient::class, 'logout']);
        Route::post('refresh', [AuthControllerClient::class, 'refresh']);
        Route::post('me', [AuthControllerClient::class, 'me']);

        // CampaignsClientController
        Route::get('campaigns', [CampaignsClientController::class, 'index']);

        // ListCustomClientController
        Route::post('/listcustom/upload', [ListCustomClientController::class, 'upload']);
        Route::post('/listcustom/status', [ListCustomClientController::class, 'report']);
        Route::post('/listcustom/reportlog', [ListCustomClientController::class, 'reporterrors']);

    });
});

Route::post('/resetpasswordrequest', [PasswordResetRequestController::class, 'sendPasswordResetEmail']);
Route::get('/changepassword', [ChangePasswordController::class, 'index']);
Route::post('/changepassword', [ChangePasswordController::class, 'passwordResetProcess']);

Route::post('/{hash}', [CalendarsController::class, 'index'])->name('linkcalender');
Route::get('/{hash}', [CalendarsController::class, 'index'])->name('linkcalender');
