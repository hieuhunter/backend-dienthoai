<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'ho_ten' => 'required',
            'user_name' => 'unique:users',
            'email' => 'unique:users'
        ];
        $messages = [
            'ho_ten.required' => 'Ho ten không được trống',
            'user_name.unique' => 'User name đã tồn tại',
            'email.unique' => 'Email đã tồn tại'
        ];
        $payload = [
            'ho_ten' => $request->ho_ten,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'sdt' => $request->sdt,
            'dia_chi' => $request->dia_chi,
            'is_admin' => $request->is_admin = 0
        ];
        $validator = Validator::make($payload, $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 200);
        }
        $user = new User($payload);
        $user->save();
        return response()->json([
            'success' => true,
            'data' =>  $user
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = request(['user_name', 'password']);
        if (!auth()->attempt($credentials))
            return response()->json([
                'success' => false,
                'errors' => [
                    "user" => "User name or password does not exists"
                ]
            ], 400);
        $tokenResult = auth()->user()->createToken('Personal Access Token');
        return response()->json([
            'success' => true,
            'data' => [
                'id' => auth()->user()->id,
                'user_name' => auth()->user()->user_name,
                'ho_ten' => auth()->user()->ho_ten,
                'sdt' => auth()->user()->sdt,
                'dia_chi' => auth()->user()->dia_chi,
                'email' => auth()->user()->email,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]
        ], 200);
    }   
    public function logout(Request $request)
    {
        Auth::user()->token()->revoke();
        return response()->json([
            'success' => true,
            'data' => 'Logout success'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => auth()->user()
        ]);
    }

}