<?php

namespace App\Transformers;

use App\Entities\Asset;
use League\Fractal\TransformerAbstract;

/**
 * Class AssetTransformer.
 */
class AssetTransformer extends TransformerAbstract
{
    /**
     * @param \App\Entities\Asset $model
     * @return array
     */
    public function transform(Asset $model)
    {
        return [
            'id' => $model->uuid,
            'type' => $model->type,
            'path' => $model->path,
            'mime' => $model->mime,
            'created_at' => $model->created_at->toIso8601String(),
            'links' => [
                'render' => url('api/assets/'.$model->uuid.'/render'),
            ],
        ];
    }
}
