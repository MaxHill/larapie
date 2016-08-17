<?php

namespace Maxhill\Api\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Maxhill\Api\Transformers\AuthTransformer;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

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

        $user = false;
        if (config('api.include_user_on_authentication')) {
            $user = User::where('email', $credentials['email'])->first();
            $this->fractal->parseIncludes('user');
        }

        return $this->respondWithItem([
            'token' => $token,
            'timeout' => $timeout,
            'user' => $user
            ], new AuthTransformer);
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
