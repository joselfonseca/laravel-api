<?php

namespace App\Contracts\Users;

use App\Entities\User;
use Joselfonseca\LaravelApiTools\Exceptions\ValidationException;

/**
 * Class UsersServiceContract
 * @package App\Contracts\Users
 */
interface UsersServiceContract
{

    /**
     * @param int $limit
     * @return mixed
     */
    public function get($limit = 20);

    /**
     * @param int|string $id
     * @return User
     */
    public function find($id);

    /**
     * @param array $attributes
     * @return User
     * @throws ValidationException
     */
    public function create(array $attributes = []);

    /**
     * @param int|string $id
     * @param array $attributes
     * @return User
     * @throws ValidationException
     */
    public function update($id, array $attributes = []);

    /**
     * @param int|string $id
     * @return bool
     */
    public function delete($id);
}
