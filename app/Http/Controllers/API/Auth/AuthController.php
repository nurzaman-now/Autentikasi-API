<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use App\Helpers\UploadImage;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register new user.
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $message = $validator->errors();
            return ResponseFormatter::validatorError($message);
        }

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            return ResponseFormatter::responseError(message: 'Kombinasi email dan password salah');
        }

        $user = Auth::user();
        $user = User::find($user->id);
        $data = [
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ];
        return ResponseFormatter::responseSuccess(data: $data, message: 'Berhasil Login');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'repassword' => 'required|string|min:8|same:password',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::validatorError($validator->errors());
        }

        // get image
        $uploadImage = new UploadImage();
        $image = $request->file('image');
        $path = 'images/users';
        $name = $request->name . '_' . $request->email;
        $path = $uploadImage->upload($image, $path, $name);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $path,
        ]);

        $user->emailVerification($request->email);

        return ResponseFormatter::responseSuccess($user, 'Berhasil Mendaftar', 201);
    }

    public function logout()
    {
        Auth::logout();
        return ResponseFormatter::responseSuccess(message: 'Berhasil Logout');
    }

    public function refresh()
    {
        $data = [
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ];
        return ResponseFormatter::responseSuccess($data, 'Berhasil Refresh Token');
    }
}
