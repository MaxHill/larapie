<?php

namespace Maxhill\Api\Http\Controllers;


use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

trait canReturnApiResponses {
    protected $statusCode = 200;

    protected $CODE_WRONG_ARGS = 'GEN-FUBARGS';
    protected $CODE_NOT_FOUND = 'GEN-LIKETHEWIND';
    protected $CODE_INTERNAL_ERROR = 'GEN-AAAGGH';
    protected $CODE_UNAUTHORIZED = 'GEN-MAYBGTFO';
    protected $CODE_FORBIDDEN = 'GEN-GTFO';

    /**
     * Setter for statusCode
     *
     * @param int $statusCode Value to set
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    protected function respondWithItem($item, $callback)
    {
        $resource = new Item($item, $callback);

        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    protected function respondWithCollection($collection, $callback)
    {
        $resource = new Collection($collection, $callback);

        $rootScope = $this->fractal->createData($resource);

        return $this->respondWithArray($rootScope->toArray());
    }

    protected function respondWithArray(array $array, array $headers = [])
    {
        $response = response()->json($array, $this->statusCode, $headers);

        return $response;
    }

    protected function respondWithError($title, $details = '', $errorCode)
    {
        if ($this->statusCode === 200) {
            trigger_error(
                "You better have a really good reason for erroring on a 200...",
                E_USER_WARNING
            );
        }

        return $this->respondWithArray(
            [
                'errors' => [
                    [
                        'code' => $errorCode,
                        'http_code' => $this->statusCode,
                        'title' => $title,
                        'details' => $details,
                    ]
                ]
            ]
        );
    }


    /**
     * Generates a Response with a 403 HTTP header and a given message.
     *
     * @return Response
     */
    public function errorForbidden($title = 'Forbidden', $details = '')
    {
        return $this->setStatusCode(403)
            ->respondWithError($title, $details, $this->CODE_FORBIDDEN);
    }

    /**
     * Generates a Response with a 500 HTTP header and a given message.
     *
     * @return Response
     */
    public function errorInternalError($title = 'Internal Error', $details = '')
    {
        return $this->setStatusCode(500)
            ->respondWithError($title, $details, $this->CODE_INTERNAL_ERROR);
    }

    /**
     * Generates a Response with a 404 HTTP header and a given message.
     *
     * @return Response
     */
    public function errorNotFound($title = 'Resource Not Found', $details = '')
    {
        return $this->setStatusCode(404)
            ->respondWithError($title, $details, $this->CODE_NOT_FOUND);
    }

    /**
     * Generates a Response with a 401 HTTP header and a given message.
     *
     * @return Response
     */
    public function errorUnauthorized($title = 'Unauthorized', $details = '')
    {
        return $this->setStatusCode(401)
            ->respondWithError($title, $details, $this->CODE_UNAUTHORIZED);
    }

    /**
     * Generates a Response with a 400 HTTP header and a given message.
     *
     * @return Response
     */
    public function errorWrongArgs($title = 'Wrong Arguments', $details = '')
    {
        return $this->setStatusCode(400)
            ->respondWithError($title, $details, $this->CODE_WRONG_ARGS);
    }
}
