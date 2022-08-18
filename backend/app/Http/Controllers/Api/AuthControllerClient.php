<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JwtPermission;
use Illuminate\Http\Request;

class AuthControllerClient extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->merge([
            'contact' => $request->email
        ]);
        $credentials = $request->only(['contact', 'password']);
        $credentials['active'] = 1;
        if (!$token = auth('apiclient')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $jwtPermission = new JwtPermission;
        $jwtPermission->token = $token;
        $jwtPermission->local = 'client';
        $jwtPermission->save();
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(['name' => auth('apiclient')->user()->name, 'contact' => auth('apiclient')->user()->contact]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('apiclient')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = auth('apiclient')->refresh();
        $jwtPermission = new JwtPermission;
        $jwtPermission->token = $token;
        $jwtPermission->local = 'client';
        $jwtPermission->save();
        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('apiclient')->factory()->getTTL() * 60
        ]);
    }
}
