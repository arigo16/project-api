<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Hash;

use App\Models\Users;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    private function decodeToken($token)
    {
        $key = env('JWT_SECRET');

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return response()->json([
                'code' => '01',
                'message' => 'Invalid token. ' . $e->getMessage(),
                'status' => 401
            ], 401);
        }
    }

    public function login(Request $request)
    {
        try {
            $validated = $this->validate($request, [
                'username'  => 'required',
                'password'  => 'required'
            ]);

            $user = Users::where('username', $validated['username'])->whereNull('deleted_at')->first();
            if ($user == null) {
                return response()->json(['status' => false, 'message' => 'Akun tidak ditemukan, coba akun lain'], 400);
            }
            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json(['status' => false, 'message' => 'Username atau password salah'], 400);
            }

            $payload = [
                'iat' => intval(microtime(true)),
                'exp' => intval(microtime(true)) + (14 * 24 * 60 * 60), // 14 hari exp
                'userid' => $user->id,
                'username' => $user->username,
                'fullname' => $user->fullname
            ];

            $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
            return response()->json(['status' => true, 'access_token' => $token], 200);
        } catch (\Throwable $th) {
            return response()->json($th);
            //throw $th;
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $decoded = $this->decodeToken($request->bearerToken());

            $validated = $this->validate($request, [
                'password'  => 'required',
                'newpassword'  => 'required',
            ]);

            $user = Users::where('id', $decoded->uid)->whereNull('deleted_at')->first();
            if ($user == null) {
                return response()->json(['status' => false, 'message' => 'Akun tidak ditemukan'], 400);
            }
            if (!Hash::check($validated['password'], $user->password)) {
                return response()->json(['status' => false, 'message' => 'Password lama yang kamu masukan salah'], 400);
            }
            if ($validated['password'] == $validated['newpassword']) {
                return response()->json(['status' => false, 'message' => 'Password baru yang dimasukan sama seperti password lama'], 400);
            }

            $user = Users::find($decoded->uid);

            $user->password = Hash::make($validated['newpassword']);
            $user->updated_by = $decoded->uname;
            $user->save();

            $res['code'] = '00';
            $res['status'] = 200;

            return response()->json($res, $res['status']);
        } catch (\Throwable $th) {
            $res['code']    = '01';
            $res['status']  = 'Error';
            $res['message'] = $th->getMessage();

            return response()->json($res, 500);
        }
    }
}
