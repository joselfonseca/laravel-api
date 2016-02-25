<?php

namespace App\Auth;

use App\Entities\Users\User;
use App\Contracts\UserResolverInterface;

/**
 * Class EloquentUserResolver
 *
 * @package App\Auth
 */
class EloquentUserResolver implements UserResolverInterface
{

    /**
     * @var User
     */
    protected $user;

    /**
     * EloquentUserResolver constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Resolve user with eloquent
     *
     * @param  $id
     * @return mixed
     */
    public function resolveById($id)
    {
        return $this->user->findOrFail($id);
    }
}
