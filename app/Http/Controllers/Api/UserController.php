<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\LoginResource;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();

        if (! $token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username atau password salah'
            ], 401);
        }

        $user = auth()->guard('api')->user();

        return (new LoginResource($user))->additional([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
            'status' => 'success',
            'message' => 'Login berhasil',
        ]);
    }

    public function logout() : JsonResponse
    {
        auth()->guard('api')->logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
