<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login_post(Request $request)
    {
        if (!User::where('email', $request->email)->exists()) {
            return redirect()->back()->with([
                'error' => [
                    "title" => "Account Not Found",
                    "message" => "Email tidak terdaftar"
                ]
            ]);
        }

        if (
            Auth::attempt([
                'email' => $request->email,
                'password' => $request->password
            ], true)
        ) {
            if (Auth::user()->role == "4") {
                return redirect()->intended('pimpinan/dashboard');
            } else if (Auth::user()->role == "3") {
                return redirect()->intended('adminsistem/dashboard');
            } else if (Auth::user()->role == "2") {
                return redirect()->intended('adminbinagram/dashboard');
            } else if (Auth::user()->role == "1") {
                return redirect()->intended('adminapproval/dashboard');
            } else if (Auth::user()->role == "0") {
                return redirect()->intended('operator/dashboard');
            }
        }

        return redirect()->back()->with([
            'error' => [
                "title" => "Invalid Credentials",
                "message" => "Username atau password salah"
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect(url('/'))->with('logout_success', 'Anda berhasil logout');
    }
}
