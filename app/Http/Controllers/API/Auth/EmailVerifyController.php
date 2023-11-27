<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Verification;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string', 'min:4', 'max:4'],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::responseError(message: $validator->errors()->first());
        }

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
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::responseError(message: $validator->errors()->first());
        }


        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return ResponseFormatter::responseError(message: 'Anda sudah verifikasi.');
        }

        $user->emailVerification($request->email);

        return ResponseFormatter::responseSuccess(null, 'Verifikasi Berhasil dikirim. silahkan lihat email anda', 201);
    }
}
