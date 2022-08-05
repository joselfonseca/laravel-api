<?php

namespace App\OAuthGrants;

use DateInterval;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class SocialGrant extends AbstractGrant
{
    public function __construct(UserRepositoryInterface $userRepository, RefreshTokenRepositoryInterface $refreshTokenRepository)
    {
        $this->setUserRepository($userRepository);
        $this->setRefreshTokenRepository($refreshTokenRepository);
        $this->refreshTokenTTL = new DateInterval('P1M');
    }

    /**
     * @inheritdoc
     */
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseTypeInterface $responseType, DateInterval $accessTokenTTL)
    {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request));
        $user = $this->validateUser($request);
        // Finalize the requested scopes
        $scopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());
        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $scopes);
        $refreshToken = $this->issueRefreshToken($accessToken);
        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    /**
     * @inheritdoc
     */
    public function getIdentifier()
    {
        return 'social_grant';
    }

    /**
     * @param  ServerRequestInterface  $request
     * @return UserEntityInterface
     *
     * @throws OAuthServerException
     */
    protected function validateUser(ServerRequestInterface $request)
    {
        $laravelRequest = new Request($request->getParsedBody());
        $user = $this->getUserEntityByRequest($laravelRequest);
        if (false === $user instanceof UserEntityInterface) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }

    /**
     * Retrieve user by request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return null|\Laravel\Passport\Bridge\User
     *
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    protected function getUserEntityByRequest(Request $request)
    {
        if (is_null($model = config('auth.providers.users.model'))) {
            throw OAuthServerException::serverError('Unable to determine user model from configuration.');
        }
        if (method_exists($model, 'byOAuthToken')) {
            $user = (new $model())->byOAuthToken($request);
        } else {
            throw OAuthServerException::serverError('Unable to find byLoggedInUser method on user model.');
        }

        return ($user) ? new User($user->id) : null;
    }
}
