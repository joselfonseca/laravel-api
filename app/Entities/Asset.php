<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Joselfonseca\LaravelApiTools\Traits\UuidScopeTrait;

/**
 * Class Asset.
 */
class Asset extends Model
{
    use UuidScopeTrait;

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'uuid', 'type', 'path', 'mime'];
}
