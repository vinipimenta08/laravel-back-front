<?php

use App\Http\Controllers\CalendarsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UploadShipping;
use App\Http\Controllers\FinancyController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TriggerSMSController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrokerSmsController;
use App\Http\Controllers\CustomerBillingController;
use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SmsProgramController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/portal/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'autentication'])->name('login-autentication');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/sessionexpire', [HomeController::class, 'sessionexpire'])->name('home.sessionexpire');

//  Route::post('/sendemail', [SendEmailController::class, 'index'])->name('sendemail');

Route::get('/password', [PasswordController::class, 'index'])->name('password');
Route::post('/password/request', [PasswordController::class, 'passwordRequest'])->name('password.request');
Route::post('/password/reset', [PasswordController::class, 'passwordResetProcess'])->name('password.reset');

Route::group(['middleware' => ['front', 'web']], function (){
    Route::get('/portal', [HomeController::class, 'layout'])->name('layout');
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::post('/', [HomeController::class, 'dashData'])->name('home.dashData');
    Route::resource('/usuarios', UserController::class);
    Route::post('usuarios/atualizar/{usuario}', [UserController::class, 'update'])->name('usuarios.atualizar');
    Route::post('/activeprofile', [UserController::class, 'active_profile'])->name('usuarios.activeprofile');
    Route::post('/activeall', [UserController::class, 'active_all'])->name('usuarios.activeall');
    Route::get('/carregaenvio', [UploadShipping::class, 'index'])->name('uploadshipping.index');
    Route::get('/uploadshipping/downloadlayout', [UploadShipping::class, 'downloadlayout'])->name('uploadshipping.downloadlayout');
    Route::post('/uploadshipping/validateupload', [UploadShipping::class, 'validateupload'])->name('uploadshipping.validateupload');
    Route::post('/uploadshipping/analyzelist', [UploadShipping::class, 'analyzelist'])->name('uploadshipping.analyzelist');
    Route::post('/uploadshipping/uploadlist', [UploadShipping::class, 'uploadlist'])->name('uploadshipping.uploadlist');
    Route::post('/uploadshipping/deletelist', [UploadShipping::class, 'deleteupload'])->name('uploadshipping.deletelist');

    Route::get('/blacklist', [BlacklistController::class, 'index'])->name('blacklist.index');
    Route::get('/blacklist/downloadlayout', [BlacklistController::class, 'downloadlayout'])->name('blacklist.downloadlayout');
    Route::get('/blacklist/downloadFile', [BlacklistController::class, 'downloadFile'])->name('blacklist.downloadFile');
    Route::post('/blacklist/importList', [BlacklistController::class, 'importList'])->name('blacklist.importList');
    Route::post('/blacklist/advancedSearch', [BlacklistController::class, 'advancedSearch'])->name('blacklist.advancedSearch');
    Route::post('/blacklist/destroyLote', [BlacklistController::class, 'destroyLote'])->name('blacklist.destroyLote');
    Route::resource('/blacklist', BlacklistController::class);

    Route::resource('/financeiro', FinancyController::class);
    Route::resource('/centrodecusto', CostCenterController::class);
    Route::post('centrodecusto/atualizar/{centrodecusto}', [CostCenterController::class, 'update'])->name('centrodecusto.atualizar');
    Route::post('/activecostcenter', [CostCenterController::class, 'active_cost_center'])->name('centrodecusto.activecostcenter');
    Route::resource('/disparosms', TriggerSMSController::class);
    Route::post('/disparosms/resendSMS', [TriggerSMSController::class, 'resendSMS'])->name('disparosms.resendSMS');
    Route::resource('/profile', ProfileController::class);
    Route::post('profile/{usuario}', [ProfileController::class, 'update'])->name('profile.atualizar');

    Route::get('/relatorio', [ReportController::class, 'index'])->name('report.index');
    Route::get('/relatorio/search', [ReportController::class, 'search'])->name('report.search');
    Route::get('/relatorio/list', [ReportController::class, 'list'])->name('report.list');
    Route::get('/relatorio/errors', [ReportController::class, 'errors'])->name('report.errors');
    Route::get('/relatorio/reply', [ReportController::class, 'reply'])->name('report.reply');

    Route::resource('/menu', MenuController::class);
    Route::post('/menu/active', [MenuController::class, 'active'])->name('menu.active');
    Route::post('/menu/atualizar/{menu}', [MenuController::class, 'atualizar'])->name('menu.atualizar');

    Route::resource('/cliente', ClientController::class);
    Route::post('/cliente/active', [ClientController::class, 'active'])->name('cliente.active');
    Route::post('/cliente/atualizar/{cliente}', [ClientController::class, 'update'])->name('cliente.atualizar');

    Route::resource('/brokersms', BrokerSmsController::class);
    Route::post('/brokersms/active', [BrokerSmsController::class, 'active'])->name('brokersms.active');
    Route::post('/brokersms/atualizar/{brokersm}', [BrokerSmsController::class, 'update'])->name('brokersms.atualizar');

    Route::resource('/customer', CustomerBillingController::class);
    Route::post('/customer/atualizar/{customer}', [CustomerBillingController::class, 'update'])->name('customer.atualizar');

    Route::resource('/program', SmsProgramController::class);
    Route::post('/program/update', [SmsProgramController::class, 'update'])->name('program.update');
    Route::post('/program/activesmsprogram', [SmsProgramController::class, 'active_sms_program'])->name('program.activesmsprogram');

});

Route::get('/{hash}', [CalendarsController::class, 'index'])->name('linkcalendar');
