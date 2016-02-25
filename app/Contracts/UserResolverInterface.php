<?php

namespace App\Contracts;

/**
 * Interface UserResolverInterface
 *
 * @package App\Auth
 */
interface UserResolverInterface
{

    /**
     * Resolve a user from ID
     *
     * @param  $id
     * @return mixed
     */
    public function resolveById($id);
}
