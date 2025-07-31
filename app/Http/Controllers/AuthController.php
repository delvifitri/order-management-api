<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'customer',
            ]);

            $accessToken = JWTAuth::fromUser($user);
            $refreshToken = $this->createRefreshToken($user);

            return response()->json([
                'message'=> 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'access_token' =>$accessToken,
                'refresh_token' =>$refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            $user = Auth::user();
            $refreshToken = $this->createRefreshToken($user);

            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'access_token' => $token,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Login failed',
                'mesage' => $e->getMessage()
            ], 500);
        }

    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        try {
            $refreshTokenRecord = RefreshToken::where('token', hash('sha256', $request->refresh_token))
                    ->where('expires_at'. '>', now())
                    ->first();
                
                if (!$refreshTokenRecord) {
                    return response()->json([
                        'error' => 'Invalid or expired refresh token'
                    ], 401);
                }

                $user = $refreshTokenRecord->user;

                $newAccessToken = JWTAuth::fromUser($user);

                return response()->json([
                    'access_token' => $newAccessToken,
                    'token_type' => 'Bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token refresh failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            JWTAuth::invalidate(JWTAuth::getToken());

            $user->refreshTokens()->delete();

            return response()->json([
                'message' => 'Logout successful'
            ]);

        } catch (\Exception $e){
            return response()->json([
                'error' => 'Logout failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
                ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unathorized'
            ], 401);
        }
    }

    private function createRefreshToken(User $user)
    {
        $refreshToken = Str::random(64);

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $refreshToken),
            'expires_at' => now()->addDays(30),
        ]);

        return $refreshToken;
    }
}
