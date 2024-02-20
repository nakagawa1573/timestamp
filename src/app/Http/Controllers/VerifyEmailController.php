<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    public function index()
    {
        return view('auth.verify_email');
    }

    public function confirm(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect('/');
    }

    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '確認用メールを再送しました');
    }
}
