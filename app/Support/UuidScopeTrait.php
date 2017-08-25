<?php

namespace App\Support;

use Uuid;

/**
 * Class UuidScopeTrait.
 */
trait UuidScopeTrait
{
    /**
     * @param $query
     * @param $uuid
     * @return mixed
     */
    public function scopeByUuid($query, $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    /**
     * Boot function from laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Uuid::generate()->string;
            }
        });
    }
}
