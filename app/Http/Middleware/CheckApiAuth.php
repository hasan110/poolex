<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class CheckApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $api_token = $request->header('x-api-key');

        if(!$api_token){
            return Response::error(null , 'کاربر احراز هویت نشده است .' , null , 401);
        }

        $user = User::where('api_token' , $api_token)->first();

        if(!$user){
            return Response::error(null , 'توکن نامعتبر است .' , null , 401);
        }

        return $next($request);
    }
}
