<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Guard;

/**
 * Class PasswordGrantVerifier
 * Verify the credentials are correct
 *
 * @package App\Auth
 */
class PasswordGrantVerifier
{

    /**
     * @var
     */
    protected $auth;

    /**
     * PasswordGrantVerifier constructor.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function verifyPasswordGrant($username, $password)
    {
        $credentials = [
            'email'    => $username,
            'password' => $password,
        ];
        if ($this->auth->once($credentials)) {
            return $this->auth->user()->id;
        }
        return false;
    }
}
