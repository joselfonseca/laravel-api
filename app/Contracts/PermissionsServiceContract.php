<?php

namespace App\Contracts;

use App\Entities\Permission;
use Dingo\Api\Exception\ResourceException;

/**
 * Class PermissionsServiceContract
 * @package App\Contracts
 */
interface PermissionsServiceContract
{

    /**
	 * @param array $attributes
	 * @param int $limit
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
	 */
    public function get(array $attributes = [], $limit = 20);

    /**
     * @param int|string $id
     * @return Permission
     */
    public function find($id);

    /**
     * @param array $attributes
     * @return Permission
     * @throws ResourceException
     */
    public function create(array $attributes = []);

    /**
     * @param int|string $id
     * @param array $attributes
     * @return Permission
     * @throws ResourceException
     */
    public function update($id, array $attributes = []);

    /**
     * @param int|string $id
     * @return bool
     */
    public function delete($id);

}