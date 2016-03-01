<?php

namespace App\Observers;

use Webpatser\Uuid\Uuid;

/**
 * Class UuidObserver
 * @package App\Observers
 */
class UuidObserver
{

    /**
     * @param $model
     * @throws \Exception
     */
    public function creating($model)
    {
        $model->uuid = Uuid::generate(4);
    }

}