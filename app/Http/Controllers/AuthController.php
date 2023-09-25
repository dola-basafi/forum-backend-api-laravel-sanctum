<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'required' => ':attribute tidak boleh kosong'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'messages' => $validator->errors()
            ], 400);
        }
        $user = User::where('username', $request['username'])->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'messages' => 'username tidak terdaftar'
            ], 400);
        }
        if (!$user || !Auth::attempt($request->only('username', 'password'))) {
            return response()->json([
                'status' => false,
                'messages' => ['username atau password anda masukkan salah']
            ], 400);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => true,
            'messages' => [
                'token' => $token,
                'id' => $user->id,
                'name' => $user->name
            ]
        ], 200);
    }
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:users'],
            'username' => ['required', 'unique:users'],
            'password' => ['required', 'min:6'],
            'name' => ['required', 'max:30'],
            'city' => ['required', 'max:30'],
        ], [
            'required' => ':attribute tidak boleh kosong',
            'unique' => ':attribute sudah pernah digunakan',
            'min' => ':attribute minimal 6 karakter',
            'email' => ':attribute bukan merupakan format email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'messages' => $validator->errors()
            ], 400);
        }
        $user = User::create([
            'name' => $request['name'],
            'city' => $request['city'],
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => $request['password'],
        ]);
        $user->assignRole('user');
        return response()->json([
            'status' => true,
            'messages' => ['berhasil membuat account']
        ]);
    }
    function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'messages' => ['berhasil logout'],
        ]);
    }
}
