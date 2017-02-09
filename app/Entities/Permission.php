<?php

namespace App\Entities;

use Joselfonseca\LaravelApiTools\Traits\UuidScopeTrait;

/**
 * Class Permission
 * @package App\Entities
 */
class Permission extends \Spatie\Permission\Models\Permission
{

    use UuidScopeTrait;

    /**
     * @var array
     */
    protected $fillable = ['name', 'uuid'];
}
