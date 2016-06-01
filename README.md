# Laravel api setup
All the api setup I dread creating for every project.
Concised down to a single plugin.

* It gives you JSON-webtoken **authentication** (using the awesome [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)).
* It gives you **model transformation** (using the fantastic [fractal-package](http://fractal.thephpleague.com/))
* It also gives you some sweet **api-helper methods** for returning resources and errors and more.


# Install
1. Add service provider.
~~~php
// config/app.php
...
Maxhill\Api\ApiServiceProvider::class,
...
~~~
2. Add route middleware.
~~~php
// app/Http/Kernel.php
...
'jwt.auth' => 'Tymon\JWTAuth\Middleware\GetUserFromToken',
'jwt.refresh' => 'Tymon\JWTAuth\Middleware\RefreshToken',
...
~~~
3. Publish vendors.
~~~bash
$ php artisan vendor:publish --provider="Maxhill\Api\ApiServiceProvider"
$ php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\JWTAuthServiceProvider"
~~~
4. Generate jwt-secret.
~~~bash
$ php artisan jwt:generate
~~~
5. **Remove** csrf-token middleware.
~~~php
// app/Http/Kernel.php
'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken',
~~~


# Usage
## Authentication
* Use route middleware

Require the user to be logged in to access a route:
~~~php
Route::post('whatever', [
    'uses' =>'WhateverController@index',
    'middleware' => 'jwt.auth'
]);
~~~
[Read more](https://github.com/tymondesigns/jwt-auth/wiki/Authentication)
* Use `authenticate` routes

Authentication is already setup. Just post to `/authenticate`
with the email and password of the user you want to sign in.

You may change the `/authenticate`-route to whatever you like in the config file `config/api.php`
## Controllers
###Extend ApiController.

Make sure your api-controllers extends
`Maxhill\Api\Http\Controllers\ApiController;` and not the default
`App\Http\Controller`

###Use transformers for returning.

Example user model transformer;
~~~php
<?php
# app/Transformers/UserTransformer.php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => (int)$user->id,
            'name' => $user->name,
            'email' => $user->email
        ];
    }

}
~~~

Using the `UserTransformer`:

~~~php
    # app/Http/Controllers/WhateverController.php

    public function index()
    {
        $users = User::all();
        return $this->respondWithCollection($users, new UserTransformer);
    }
~~~

###ApiController - methods
* `user() // Returns false or user object parsed from auth token`

#####Respond with data:
* `respondWithItem()`
* `respondWithCollection()`
* `respondWithArray()`

#####Error-responses:
* `respondWithError()`
* `errorForbidden()`
* `errorInternalError()`
* `errorNotFound()`
* `errorUnauthorized()`
* `errorWrongArgs()

