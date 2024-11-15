<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthAction;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\AuthResource;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthController extends Controller
{
    protected $authAction;

    public function __construct(
        AuthAction $authAction
    )
    {
        $this->authAction = $authAction;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $user = $this->authAction->register($request->validated());
            $token = JWTAuth::fromUser($user);

            return ResponseHelper::nonPagedResponse(new AuthResource($token), HttpResponse::HTTP_OK);

        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, 'Failed to register user: ' . $e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return ResponseHelper::errorResponse( HttpResponse::HTTP_UNAUTHORIZED, 'Invalid credentials');
            }

            return ResponseHelper::nonPagedResponse(new AuthResource($token),  HttpResponse::HTTP_OK);
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, 'Failed to login: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        try {
            $this->authAction->logout();

            return ResponseHelper::nonPagedResponse(['message' => 'User logged out successfully'],  HttpResponse::HTTP_OK);
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }
}
