<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{
    public function create()
    {
        $today = date('Y-m-d');
        $nik = Auth::guard('karyawan')->user()->id;
        $cek = Presensi::where('nik', $nik)->where('tgl_presensi', $today)->count();
        return view('presensi.create', [
            'cek' => $cek
        ]);
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->id;
        $tgl_presensi = date('Y-m-d');
        $jam = date('H:i:s');
        $lokasi = $request->lokasi;
        $image = $request->image;

        $folderPath = "public/uploads/absensi/";
        $formatName = $nik . "-" . $tgl_presensi;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        $data = [
            'nik' => $nik,
            'tgl_presensi' => $tgl_presensi,
            'jam_in' => $jam,
            'lokasi_in' => $lokasi,
            'foto_in' => $fileName
        ];

        $cek_presensi = Presensi::where('nik', $nik)->where('tgl_presensi', $tgl_presensi)->count();
        if ($cek_presensi) {
            $data_pulang = [
                'jam_out' => $jam,
                'lokasi_out' => $lokasi,
                'foto_out' => $fileName
            ];
            $presensi_masuk = Presensi::select('id')->where('nik', $nik)->where('tgl_presensi', $tgl_presensi)->first();
            $simpan = Presensi::where('id', $presensi_masuk->id)->update($data_pulang);
        } else {
            $simpan = Presensi::create($data);
        }

        if ($simpan) {
            Storage::put($file, $image_base64);
            echo 0;
        } else {
            echo 1;
        }
    }
}
