<?php

namespace App\Entities;

use Joselfonseca\LaravelApiTools\Traits\UuidScopeTrait;

/**
 * Class Permission.
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    use UuidScopeTrait;

    /**
     * @var array
     */
    protected $fillable = ['name', 'uuid', 'guard_name'];
}
