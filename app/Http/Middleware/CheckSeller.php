<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

class CheckSeller
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
        $api_token = $request->header('api_token');

        $user = User::where('api_token' , $api_token)->first();

        if(intval($user->is_seller) !== 1){
            return Response::error(null , 'شما مجاز به استفاده از پنل فروشندگان نیستید .' , null , 401);
        }

        return $next($request);
    }
}
