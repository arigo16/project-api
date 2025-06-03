<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Kendaraan;
use App\Models\Users;
use Carbon\Carbon;

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

    public function saveKendaraan(Request $request)
    {
        try {
            Kendaraan::create([
                'name' => $request->name,
                'merk' => $request->merk,
                'color' => $request->color,
            ]);

            $res['code'] = '00';
            $res['message'] = 'Success';
            $res['status'] = 200;
        } catch (\Exception $e) {
            $res['code'] = '39';
            $res['message'] = 'Gagal menyimpan data kendaraan karena' . $e->getMessage();
            $res['status'] = 400;
        }

        return response()->json($res, $res['status']);
    }

    public function editKendaraan(Request $request)
    {
        try {
            $kendaraan = Kendaraan::where('id', $request->id)
                ->first();

            if ($kendaraan) {
                $kendaraan->name = $request->name;
                $kendaraan->merk = $request->merk;
                $kendaraan->color = $request->color;
                $kendaraan->save();

                $res['code'] = '00';
                $res['status'] = 200;
            } else {
                $res['code'] = '02';
                $res['message'] = 'Kendaraan Tidak Ditemukan';
                $res['status'] = 404;
            }
        } catch (\Exception $e) {
            $res['code'] = '01';
            $res['message'] = 'Failed to edit kendaraan. ' . $e->getMessage();
            $res['status'] = 400;
        }

        return response()->json($res, $res['status']);
    }

    public function hardDeleteKendaraan(Request $request)
    {
        try {
            $kendaraan = Kendaraan::find($request->id);

            if ($kendaraan) {
                $kendaraan->delete();

                $res['code'] = '00';
                $res['message'] = 'Kendaraan Berhasil Dihapus';
                $res['status'] = 200;
            } else {
                $res['code'] = '02';
                $res['message'] = 'Kendaraan Tidak Ditemukan';
                $res['status'] = 404;
            }
        } catch (\Exception $e) {
            $res['code'] = '01';
            $res['message'] = 'Failed to delete kendaraan. ' . $e->getMessage();
            $res['status'] = 400;
        }

        return response()->json($res, $res['status']);
    }

    public function softDeleteKendaraan(Request $request)
    {
        try {
            $kendaraan = Kendaraan::find($request->id);

            if ($kendaraan) {
                $kendaraan->deleted_at = Carbon::now();
                $kendaraan->save();

                $res['code'] = '00';
                $res['message'] = 'Kendaraan Berhasil Dihapus';
                $res['status'] = 200;
            } else {
                $res['code'] = '02';
                $res['message'] = 'Kendaraan Tidak Ditemukan';
                $res['status'] = 404;
            }
        } catch(\Exception $e) {
            $res['code'] = '01';
            $res['message'] = 'Failed to delete kendaraan. ' . $e->getMessage();
            $res['status'] = 400;
        }

        return response()->json($res, $res['status']);
    }

    public function kelasMalamFun()
    {
        try {
            $data = Kendaraan::whereNull('deleted_at')->get();

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
