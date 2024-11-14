<?php

namespace App\Actions\Auth;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthAction
{
    public function register($data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function login($credentials)
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        return $token;
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            throw new \Exception('Failed to logout');
        }
    }
}
