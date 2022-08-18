<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LibraryController;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Session;

class FrontendMiddleware
{
    protected $route;


    public function __construct(Route $route)
    {
        $this->route = $route;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        $libraryController = new LibraryController;
        try {
            $body = [
                'ip' => $request->ip(),
                'route' => $this->route->getAction()['as'],
                'method' => $request->method()
            ];
            $libraryController->requestAsync("POST", "/api/logsystem/lognavegation", $body);

            if (!Session::has('credentials')) {
                if (isset($request->validate_loged)) {
                    return redirect(route('home.sessionexpire'));
                }else {
                    return redirect(route('login'));
                }
            }else {
                $date = Session::get('last_activity');
                $datework = Carbon::createFromDate($date);
                $now = Carbon::now();
                $testdate = $datework->diffInMinutes($now);
                if ($testdate >= env('SESSION_EXPIRE')) {
                    LoginController::logout();
                    return redirect(route('login'));
                }
                if (Carbon::now()->format('Y-m-d H:i:s') > Session::get('expires_in')) {
                    LoginController::refresh();
                }
                Session::put('last_activity', Carbon::now()->format('Y-m-d H:i:s'));
                return $next($request);
            }
        } catch (Exception $e) {
            Session::forget('credentials');
            Session::forget('nameUser');
            Session::forget('last_activity');
            return redirect(route('login'));
        }
    }
}
