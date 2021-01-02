<?php

namespace App\Models;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Permission.
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    use UuidScopeTrait, HasFactory;

    /**
     * @var array
     */
    protected $fillable = ['name', 'uuid', 'guard_name'];
}
