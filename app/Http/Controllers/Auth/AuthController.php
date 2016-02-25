<?php

namespace App\Http\Controllers\Auth;

use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use LucaDegasperi\OAuth2Server\Authorizer;

/**
 * Class AuthController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    use Helpers;

    /**
     * @var
     */
    protected $authorizer;

    /**
     * AuthController constructor.
     * @param Authorizer $authorizer
     */
    public function __construct(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;
    }

    /**
     * Log in the user with the oAuth2 server
     * @return mixed
     */
    public function login()
    {
        return $this->response->array($this->authorizer->issueAccessToken());
    }

    /**
     * Refresh the user's token
     * @return mixed
     */
    public function refreshToken()
    {
        return $this->response->array($this->authorizer->issueAccessToken());
    }

}
