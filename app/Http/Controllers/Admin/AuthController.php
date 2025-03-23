<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
            return $this->failResponse('Invalid credentials');
        }

        $user = auth()->user();
        $refreshToken = JWTAuth::claims(['refresh' => true])
            ->fromUser($user);
        $cookie = $this->createRefreshTokenCookie($refreshToken);

        return $this->successResponse(data: [
            'user' => $user,
            'access_token' => $token,
        ])->withCookie($cookie);
    }

    public function logout()
    {
        auth()->logout();
        return $this->successResponse('Logged out successfully')
            ->withoutCookie('refresh_token');
    }

    public function refresh()
    {
        try {
            $refreshToken = request()->cookie('refresh_token');
            if (!$refreshToken) {
                return $this->respondUnAuthenticated();
            }

            // Attempt to refresh the access token
            $newAccessToken = JWTAuth::setToken($refreshToken)->refresh();

            // Get the authenticated user
            $user = Auth::guard('api')->setToken($newAccessToken)->user();

            // Create new refresh token to reset the expiry of it
            $newRefreshToken = JWTAuth::fromUser($user, ['refresh' => true]);
            $cookie = $this->createRefreshTokenCookie($newRefreshToken);

            return $this->successResponse(
                data: [
                    'user' => $user,
                    'access_token' => $newAccessToken
                ]
            )->withCookie($cookie);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->respondUnAuthenticated('Invalid refresh token');
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->respondUnAuthenticated('Refresh token has expired');
        }
    }

    public function me()
    {
        return $this->successResponse(data: [
            'user' => auth()->user(),
        ]);
    }

    public function clearCookies()
    {
        return $this->successResponse()
            ->withoutCookie('refresh_token');
    }

    public function createRefreshTokenCookie($refreshToken): Cookie
    {
        return cookie(
            name: 'refresh_token',
            value: $refreshToken,
            minutes: config('jwt.refresh_ttl'),
            path: null,
            domain: null,
            secure: config('app.env') !== 'local',
            httpOnly: true,
        );
    }
}
