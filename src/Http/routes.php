<?php

// Authenticate
Route::post(
    config('api.authentication_route'),
    'Maxhill\Api\Http\Controllers\AuthenticateController@authenticate'
);