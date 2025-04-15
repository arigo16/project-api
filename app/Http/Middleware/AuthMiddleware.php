<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\EmployeePersonal;
use App\Models\Users;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if (empty($token)) {
            $res['code']    = '01';
            $res['status']  = 'ERROR';
            $res['message'] = 'Unauthorized';
            return response()->json($res, 401);
        }else{
            try{
                $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
                $data    = Users::find($decoded->id);
                $request->auth = $data;
                return $next($request);
            }catch(\Throwable $th){
                $res['code']    = '01';
                $res['status']  = 'ERROR';
                $res['message'] = 'Invalid Authorization';
                return response()->json($res, 401);
            }
        }
    }
}
