<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Verification;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerifyController extends Controller
{
    public function notice(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ResponseFormatter::responseError(message: 'Anda sudah verifikasi.');
        }
        return ResponseFormatter::responseError(message: 'Verifikasi email anda!!!.');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string', 'min:4', 'max:4'],
        ]);

        $user = User::where('email', $request->email)->first();
        // check user
        if (!$user) {
            return ResponseFormatter::responseError(message: 'User tidak ditemukan');
        }
        // Check token
        if ($user->hasVerifiedEmail()) {
            return ResponseFormatter::responseSuccess(message: 'Anda sudah verifikasi.');
        }

        $emailVerification = Verification::where([
            'email' => $request->email,
            'token' => $request->token,
        ])->first();

        if ($emailVerification != null &&  $user->markEmailAsVerified()) {
            event(new Verified($user));
            $emailVerification->delete();
        } else {
            return ResponseFormatter::responseError(message: 'Kode verifikasi Salah');
        }

        return ResponseFormatter::responseSuccess(null, 'Berhasil Diverifikasi', 201);
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return ResponseFormatter::responseError(message: 'Anda sudah verifikasi.');
        }

        $user->emailVerification($request->email);

        return ResponseFormatter::responseSuccess(null, 'Verifikasi Berhasil dikirim. silahkan lihat email anda', 201);
    }
}
