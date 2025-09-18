<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->safe()->all();

            if (!Auth::attempt($validated)) {
                return response()->json([
                    'message' => 'Email atau password salah',
                    'data' => null
                ], 401);
            }

            $user = $request->user();
            $token = $user->createToken('laravel_api', ['*'])->plainTextToken;

            return response()->json([
                'message' => 'login Berhasil',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->safe()->all();
            $passwordHash = Hash::make($validated['password']);
            $validated['password'] = $passwordHash;
            $response = User::create($validated);

            if ($response) {
                return response()->json([
                    'message' => 'registered berhasil',
                    'data' => null
                ], 201);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    public function logout() {}
}
