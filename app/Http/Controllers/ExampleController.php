<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kendaraan;
use App\Models\Users;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function testFun()
    {
        try {
            $data = Kendaraan::get();

            $res['code']    = '00';
            $res['data']    = $data;
            $res['message']  = 'Success';

            return response()->json($res, 200);
        } catch (\Throwable $th) {
            $res['code']    = '01';
            $res['status']  = 'Error';
            $res['message'] = $th->getMessage();

            return response()->json($res, 500);
        }
    }

    public function kelasMalamFun()
    {
        try {
            $data = Kendaraan::where('color', 'Blue')->get();

            $res['code']    = '00';
            $res['data']    = $data;
            $res['message']  = 'Success';

            return response()->json($res, 200);
        } catch (\Throwable $th) {
            $res['code']    = '01';
            $res['status']  = 'Error';
            $res['message'] = $th->getMessage();

            return response()->json($res, 500);
        }
    }
}
