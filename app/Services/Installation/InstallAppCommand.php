<?php

namespace App\Services\Installation;

/**
 * Class InstallAppCommand.
 */
class InstallAppCommand
{
    /**
     * @var
     */
    public $name;

    /**
     * @var
     */
    public $email;

    /**
     * @var
     */
    public $password;

    /**
     * @var
     */
    public $password_confirmation;

    /**
     * InstallAppCommand constructor.
     *
     * @param $name
     * @param $email
     * @param $password
     * @param $password_confirmation
     */
    public function __construct(
        $name = 'Administrator',
        $email = 'admin@admin.com',
        $password = 'secret1234',
        $password_confirmation = 'secret1234'
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->password_confirmation = $password_confirmation;
    }
}
