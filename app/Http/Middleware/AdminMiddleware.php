<?php

namespace App\Http\Middleware;

use App\Models\AdminModel;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //VALIDATE USER
        $session_token = false;
        if (session('login') && is_object(session('login')) && property_exists(session('login'), "token")) {
            $session_token = session('login')->token;
        }
        $token = request()->header('token', $session_token);


        if (!request()->header('token') && !$session_token) {
            return response()->json([
                'error' => [
                    'title' => 'token is missing',
                    'message' => 'This API requires an authentication token in order to run properly'
                ],
            ], 400);
        }

        $admin = AdminModel::where('cancelled', 0)->where('token', $token)->first();
        if (!$admin) {
            return response()->json([
                'error' => [
                    'title' => 'token is missing',
                    'message' => 'This API requires an authentication token in order to run properly'
                ],
            ], 400);
        }

        unset($admin->password);
        $return['column'] = "admin";
        $return['admin'] = $admin;
        $return['token'] = $admin["token"];
        $request->admin = collect($return);
        return $next($request);
    }
}
