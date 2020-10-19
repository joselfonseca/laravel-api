<?php

namespace App\Events;

use App\Models\Asset;

/**
 * Class AssetWasCreated.
 */
class AssetWasCreated
{
    /**
     * @var \App\Models\Asset
     */
    public $asset;

    /**
     * AssetWasCreated constructor.
     *
     * @param \App\Models\Asset $asset
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }
}
