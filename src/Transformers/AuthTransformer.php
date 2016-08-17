<?php
namespace Maxhill\Api\Transformers;

use League\Fractal\TransformerAbstract;

class AuthTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'user'
    ];

    /**
     * Turn this item object into a generic array
     *
     * @param User $user
     * @return array
     */
    public function transform($credentials)
    {
        return [
            'token' => $credentials['token'],
            'timeout' => $credentials['timeout']
        ];
    }

    /**
     * Include User
     *
     * @return League\Fractal\ItemResource
     */
    public function includeUser($credentials)
    {
        $user = $credentials['user'];
        $userTransformer = config('api.user_transformer');
        return $this->item($user, new $userTransformer);
    }
}
