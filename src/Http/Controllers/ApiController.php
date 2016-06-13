<?php

namespace Maxhill\Api\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use League\Fractal\Manager;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class ApiController extends Controller
{
    use canReturnApiResponses;

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;

        if (Input::get('include')) {
            $this->fractal->parseIncludes(Input::get('include'));
        }
    }

    /**
     * Get user if possible otherwise return false
     * @return mixed false or user object
     */
    public function user()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            $user = false;
        } catch (JWTException $e) {
            $user = false;
        }

        return $user;
    }
}
