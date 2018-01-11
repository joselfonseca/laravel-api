<?php

namespace App\Events;

use App\Entities\Asset;

/**
 * Class AssetWasCreated.
 */
class AssetWasCreated
{
    /**
     * @var \App\Entities\Asset
     */
    public $asset;

    /**
     * AssetWasCreated constructor.
     *
     * @param \App\Entities\Asset $asset
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }
}
