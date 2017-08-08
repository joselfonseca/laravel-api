<?php

namespace App\Entities\Assets;

use App\Support\UuidScopeTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Asset.
 */
class Asset extends Model
{
    use UuidScopeTrait;

    /**
     * @var array
     */
    protected $guarded = ['id'];
}
