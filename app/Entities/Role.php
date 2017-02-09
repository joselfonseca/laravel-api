<?php

namespace App\Entities;

use Joselfonseca\LaravelApiTools\Traits\UuidScopeTrait;

/**
 * Class Role
 * @package App\Entities
 */
class Role extends \Spatie\Permission\Models\Role
{

    use UuidScopeTrait;

    /**
     * @var array
     */
    protected $fillable = ['name', 'uuid'];
}
