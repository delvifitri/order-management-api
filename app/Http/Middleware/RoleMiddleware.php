<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles = []): Response
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $userRole = $payload->get('role');

            if ($roles && in_array($userRole, $roles)) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Insufficient permissions'
                ], 403);
            }
            
        } catch (\exception $e) {
            return response() -> json([
                'error' => 'Unauthorized',
                'message' => 'This action is unauthorized'
            ], 401);
        }
        return $next($request);
    }
}
