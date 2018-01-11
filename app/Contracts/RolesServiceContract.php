<?php

namespace App\Contracts;

use App\Entities\Role;
use Dingo\Api\Exception\ResourceException;

/**
 * Class RolesServiceContract.
 */
interface RolesServiceContract
{
    /**
     * @param array $attributes
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get(array $attributes = [], $limit = 20);

    /**
     * @param int|string $id
     * @return Role
     */
    public function find($id);

    /**
     * @param array $attributes
     * @return Role
     * @throws ResourceException
     */
    public function create(array $attributes = []);

    /**
     * @param int|string $id
     * @param array $attributes
     * @return Role
     * @throws ResourceException
     */
    public function update($id, array $attributes = []);

    /**
     * @param int|string $id
     * @return bool
     */
    public function delete($id);
}
