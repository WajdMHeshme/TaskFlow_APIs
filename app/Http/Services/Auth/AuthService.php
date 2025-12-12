<?php

namespace App\Http\Services\Auth;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    public function register($request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'user',
            'password' => Hash::make($data['password'])
        ]);

        return $user;
    }

    public function login($request)
    {
        $data = $request->validated();
        if (!Auth::attempt($data)) {
            throw new Exception("Unvalid email or password");
        }
        $user = User::where('email', $data['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        $outputData =
            [
                "user" => $user,
                "token" => $token
            ];
        return $outputData;
    }

    public function logout($request)
    {
        $request->user()->currentAccessToken()->delete();
        return true;
    }

}
