<?php

namespace App\Services\Installation;

use Joselfonseca\LaravelTactician\CommandBusInterface;
use Joselfonseca\LaravelTactician\Middleware\DatabaseTransactions;

/**
 * Class AppInstallationService
 * @package App\Services\Installation
 */
class AppInstallationService implements AppInstallationServiceContract
{

    /**
     * @var CommandBusInterface
     */
    protected $bus;

    /**
     * @var array
     */
    protected $middleware = [
        DatabaseTransactions::class
    ];

    /**
     * AppInstallationService constructor.
     * @param CommandBusInterface $bus
     */
    public function __construct(CommandBusInterface $bus)
    {
        $this->bus = $bus;
    }


    /**
     * @param array $installationData
     * @return mixed
     */
    public function installApp(array $installationData = [])
    {
        $this->bus->addHandler(InstallAppCommand::class, InstallAppHandler::class);
        return $this->bus->dispatch(InstallAppCommand::class, $installationData, $this->middleware);
    }
}
