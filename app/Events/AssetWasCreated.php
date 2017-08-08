<?php

namespace App\Events;

use App\Entities\Assets\Asset;

/**
 * Class AssetWasCreated.
 */
class AssetWasCreated
{
    /**
     * @var \App\Entities\Assets\Asset
     */
    public $asset;

    /**
     * AssetWasCreated constructor.
     *
     * @param \App\Entities\Assets\Asset $asset
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }
}
