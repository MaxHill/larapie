<?php

namespace Maxhill\Api\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends ApiController
{
    public function authenticate(Request $request)
    {

        $credentials = $request->only('email', 'password');
        $ttl = config('jwt.ttl');
        $timeout = Carbon::now()->addMinutes($ttl)->toDateTimeString();

        try {
            $token = $this->generateToken($credentials);
            if (!$token) {
                return $this->errorUnauthorized('Invalid credentials');
            }
        } catch (JWTException $e) {
            return $this->errorInternalError('Could not create token');
        }

        return $this->respondWithArray([
            'data' => [
                'token' => $token,
                'timeout' => $timeout,
            ]
        ]);
    }

    /**
     * @param $credentials
     * @return mixed
     */
    private function generateToken($credentials)
    {

        return JWTAuth::attempt($credentials);
    }
}