<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * JwtAuthenticator middleware to authenticate requests using JWT
 */
class JwtAuthenticator
{
    /**
     * Handle the incoming request
     * 
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {

            JWT::decode($token, new Key(config('jwt.secret'), config('jwt.algorithm')));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token',  'message' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}