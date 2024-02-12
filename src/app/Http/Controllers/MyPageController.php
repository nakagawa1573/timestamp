<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\TwoFactorAuthPassword;
use App\Models\User;

class MyPageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('mypage', compact('user'));
    }

    public function tfa()
    {
        return view('two_factor_challenge');
    }

    public function sendEmail(Request $request)
    {
        $info = $request->only('email', 'password');
        if (Auth::validate($info)) {
            $random_code = '';

            for ($i=0; $i < 6; $i++) {
                $random_code .= strval(rand(0, 9));
            }

            $user = User::where('email', $request->email)->first();
            $user->two_factor_secret = $random_code;
            $user->tfa_expiration = now()->addMinutes(10);
        }
    }
}
