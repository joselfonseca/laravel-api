<?php

namespace App\Contracts;


/**
 * Interface ClientResolverInterface
 *
 * @package App\Contracts
 */
interface ClientResolverInterface
{

    /**
     * Resolve a oAuth 2 client by ID
     *
     * @param  $id
     * @return mixed
     */
    public function resolveById($id);

}