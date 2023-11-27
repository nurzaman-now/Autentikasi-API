<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Models\Verification;
use App\Notifications\EmailMessage;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as RulesPassword;

class ResetPassController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::validatorError($validator->errors());
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ResponseFormatter::responseError('Email tidak terdaftar.');
        }
        $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT); // Nomor acak empat digit
        $token = Password::createToken($user);
        $verification = Verification::create([
            'email' => $request->email,
            'token' => $verificationCode,
            'reset_token' => $token,
            'type' => 'passwordReset'
        ]);


        if ($token && $verification) {
            // Send the custom reset link via email
            Notification::route('mail', $request->email)->notify(new EmailMessage('Lupa Password', 'untuk mereset password anda dikarenakan lupa', $verificationCode));

            return ResponseFormatter::responseSuccess(true, 'Token berhasil dikirim ke email. Silahkan cek email anda!', 201);
        }

        return ResponseFormatter::responseError('Gagal mengirimkan token.');
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::validatorError($validator->errors());
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return ResponseFormatter::responseError('Email tidak terdaftar.');
        }

        $passwordReset = Verification::where([
            'email' => $request->email,
            'token' => $request->token,
            'type' => 'passwordReset'
        ])->first();

        if (isset($passwordReset)) {
            $data = $request->only('email', 'password', 'password_confirmation');
            $data['token'] = $passwordReset->reset_token;
            $status = Password::reset(
                $data,
                function ($user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => str::random(60),
                    ])->save();

                    $user->tokens()->delete();

                    event(new PasswordReset($user));
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                $passwordReset->delete();
                return ResponseFormatter::responseSuccess(true, 'Password berhasil di reset', 201);
            }
        }

        return ResponseFormatter::responseError('Password gagal di reset.');
    }
}
