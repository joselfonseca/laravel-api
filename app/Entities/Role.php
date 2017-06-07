<?php

namespace App\Entities;

use App\Support\UuidScopeTrait;

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
