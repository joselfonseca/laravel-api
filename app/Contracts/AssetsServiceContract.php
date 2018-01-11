<?php

namespace App\Contracts;

use App\Entities\Asset;
use Dingo\Api\Exception\ResourceException;

/**
 * Class AssetsServiceContract
 * @package App\Contracts
 */
interface AssetsServiceContract
{

    /**
     * @param array $attributes
     * @param int $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get(array $attributes = [], $limit = 20);

    /**
     * @param int|string $id
     * @return Asset
     */
    public function find($id);

    /**
     * @param array $attributes
     * @return Asset
     * @throws ResourceException
     */
    public function create(array $attributes = []);

    /**
     * @param int|string $id
     * @param array $attributes
     * @return Asset
     * @throws ResourceException
     */
    public function update($id, array $attributes = []);

    /**
     * @param int|string $id
     * @return bool
     */
    public function delete($id);

    /**
     * @param array $attributes
     * @return mixed
     */
    public function uploadFromUrl($attributes = []);

    /**
     * @param array $attributes
     * @return mixed
     */
    public function uploadFromDirectFile($attributes = []);
}
