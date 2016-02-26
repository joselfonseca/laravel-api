<?php

namespace App\Auth;

use App\Contracts\ClientResolverInterface;
use App\Entities\Auth\Client;

/**
 * Class EloquentClientResolver
 *
 * @package App\Auth
 */
class EloquentClientResolver implements ClientResolverInterface
{

    /**
     * @var
     */
    protected $model;

    /**
     * EloquentClientResolver constructor.
     *
     * @param Client $model
     */
    public function __construct(Client $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function resolveById($id)
    {
        return $this->model->where('id', $id)->firstOrFail();
    }

}