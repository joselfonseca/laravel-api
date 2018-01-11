<?php

namespace App\Contracts;

use App\Entities\User;
use Dingo\Api\Exception\ResourceException;

/**
 * Class UsersServiceContract.
 */
interface UsersServiceContract
{
    /**
     * @param array $attributes
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get(array $attributes = [], $limit = 20);

    /**
     * @param int|string $id
     * @return User
     */
    public function find($id);

    /**
     * @param array $attributes
     * @return User
     * @throws ResourceException
     */
    public function create(array $attributes = []);

    /**
     * @param int|string $id
     * @param array $attributes
     * @param bool $partial
     * @return User
     * @throws ResourceException
     */
    public function update($id, array $attributes = [], $partial = false);

    /**
     * @param int|string $id
     * @return bool
     */
    public function delete($id);

    /**
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function updatePassword($id, array $attributes = []);
}
