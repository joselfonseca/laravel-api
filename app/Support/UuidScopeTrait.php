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
     * Boot the uuid scope trait for a model.
     *
     * @return void
     */
    protected static function bootUuidScopeTrait()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Uuid::generate()->string;
            }
        });
    }
}
