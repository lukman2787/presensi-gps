<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        // $pass = '123456';
        // echo Hash::make($pass);

        if (Auth::guard('karyawan')->attempt(['id' => $request->nik, 'password' => $request->password])) {
            return redirect('/dashboard');
        } else {

            return redirect('/')->with(['warning' => 'NIK / Password Salah']);
        }
    }

    public function proseslogout(Request $request)
    {
        if (Auth::guard('karyawan')->check()) {
            Auth::guard('karyawan')->logout();
            return redirect('/');
        }
    }
}
