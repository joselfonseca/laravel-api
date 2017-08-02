<?php

namespace App\Services\Installation\Events;

use App\Entities\User;

/**
 * Class ApplicationWasInstalled.
 */
class ApplicationWasInstalled
{
    /**
     * @var
     */
    public $adminUser;

    /**
     * @var
     */
    public $roles;

    /**
     * @var
     */
    public $permissions;

    /**
     * ApplicationWasInstalled constructor.
     *
     * @param User $adminUser
     * @param $roles
     * @param $permissions
     */
    public function __construct(User $adminUser, $roles, $permissions)
    {
        $this->adminUser = $adminUser;
        $this->roles = $roles;
        $this->permissions = $permissions;
    }
}
