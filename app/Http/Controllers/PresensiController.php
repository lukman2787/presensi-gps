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

        $latitudekantor = -6.7049707111210735; // spbu rawagatel
        $longitudekantor = 108.41258614593008;;

        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nik . "-" . $tgl_presensi;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $cek_presensi = Presensi::where('nik', $nik)->where('tgl_presensi', $tgl_presensi)->count();

        if ($radius > 75) {
            echo "error|Maaf anda berada di luar radius, Jarak Anda adalah " . $radius . " meter dari kantor|radius";
        } else {

            if ($cek_presensi) {
                $data_pulang = [
                    'jam_out' => $jam,
                    'lokasi_out' => $lokasi,
                    'foto_out' => $fileName
                ];
                $presensi_masuk = Presensi::select('id')->where('nik', $nik)->where('tgl_presensi', $tgl_presensi)->first();
                $update = Presensi::where('id', $presensi_masuk->id)->update($data_pulang);
                if ($update) {
                    Storage::put($file, $image_base64);
                    echo "success|Terima kasih, hati-hati dijalan|out";
                } else {
                    echo "error|Maaf gagal absen, hubungi tim IT|out";
                }
            } else {
                $data = [
                    'nik' => $nik,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'lokasi_in' => $lokasi,
                    'foto_in' => $fileName
                ];

                $simpan = Presensi::create($data);
                if ($simpan) {
                    Storage::put($file, $image_base64);
                    echo "success|Terima kasih, Selamat bekerja|in";
                } else {
                    echo "error|Maaf gagal absen, hubungi tim IT|in";
                }
            }
        }
    }

    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }
}
