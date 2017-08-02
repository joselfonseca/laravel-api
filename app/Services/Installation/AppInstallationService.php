<?php

namespace App\Services\Installation;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;

/**
 * Class AppInstallationService.
 */
class AppInstallationService implements AppInstallationServiceContract
{
    protected $pipeline;

    /**
     * @var array
     */
    protected $middleware = [
        InstallAppHandler::class,
    ];

    public function __construct(Pipeline $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @param array $installationData
     * @return mixed
     */
    public function installApp(array $installationData = [])
    {
        return DB::transaction(function () use ($installationData) {
            $this->pipeline->send((object) $installationData)->through($this->middleware)->then(function ($installation) {
                return $installation;
            });
        });
    }
}
