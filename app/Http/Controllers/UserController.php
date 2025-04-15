<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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

    public function GetListUser()
    {
        try {
            $data = Users::whereNull('deleted_at')
                ->orderByDesc('id')
                ->get();

            $res['code']    = '00';
            $res['data']    = $data;
            $res['status']  = 'Success';

            return response()->json($res, 200);
        } catch (\Throwable $th) {
            $res['code']    = '01';
            $res['status']  = 'Error';
            $res['message'] = $th->getMessage();

            return response()->json($res, 500);
        }
    }

    public function GetListUserAdminNCS()
    {
        try {
            $data = Users::where('role', 'ADMIN NCS')
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->get();

            $res['code']    = '00';
            $res['data']    = $data;
            $res['status']  = 'Success';

            return response()->json($res, 200);
        } catch (\Throwable $th) {
            $res['code']    = '01';
            $res['status']  = 'Error';
            $res['message'] = $th->getMessage();

            return response()->json($res, 500);
        }
    }

    public function SaveUser(Request $request)
    {
        $decoded = $this->decodeToken($request->bearerToken());
        DB::beginTransaction();

        try {
            $usernameExists = Users::where('username', $request->username)
                ->whereNull('deleted_at')
                ->exists();

            if ($usernameExists) {
                $res['code'] = '01';
                $res['message'] = 'Username sudah dimiliki akun lain';
                $res['status'] = 400;
            } else {
                Users::create([
                    'username' => $request->username,
                    'fullname' => $request->fullname,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'created_by' => $decoded->id
                ]);

                DB::commit();

                $res['code'] = '00';
                $res['status'] = 200;
            }
        } catch (\Exception $e) {
            DB::rollBack();

            $res['code'] = '01';
            $res['message'] = 'Failed to save user. ' . $e->getMessage();
            $res['status'] = 400;
        }

        return response()->json($res, $res['status']);
    }

    public function EditUser(Request $request)
    {
        $decoded = $this->decodeToken($request->bearerToken());

        try {
            $user = Users::where('id', $request->id)
                ->whereNull('deleted_at')
                ->first();

            if ($user) {
                $user->username = $request->username;
                $user->fullname = $request->fullname;
                $user->role = $request->role;
                $user->updated_by = $decoded->id;
                $user->save();

                $res['code'] = '00';
                $res['status'] = 200;
            } else {

                $res['code'] = '02';
                $res['message'] = 'Data Not Found';
                $res['status'] = 404;
            }
        } catch (\Exception $e) {

            $res['code'] = '01';
            $res['message'] = 'Failed to edit user. ' . $e->getMessage();
            $res['status'] = 400;
        }

        return response()->json($res, $res['status']);
    }

    public function DeleteUser(Request $request)
    {
        $decoded = $this->decodeToken($request->bearerToken());

        try {
            $user = Users::where('id', $request->id)
                ->whereNull('deleted_at')
                ->first();

            if ($user) {
                $user->deleted_by = $decoded->id;
                $user->deleted_at = Carbon::now();
                $user->save();

                $res['code'] = '00';
                $res['status'] = 200;
            } else {
                $res['code'] = '02';
                $res['message'] = 'Data Not Found or Deleted';
                $res['status'] = 404;
            }
        } catch (\Exception $e) {
            $res['code'] = '01';
            $res['message'] = 'Failed to deleted user. ' . $e->getMessage();
            $res['status'] = 400;
        }

        return response()->json($res, $res['status']);
    }
}
