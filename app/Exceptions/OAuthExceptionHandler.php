<?php

namespace App\Exceptions;

use Response;
use League\OAuth2\Server\Exception\OAuthException;
use League\OAuth2\Server\Exception\InvalidRefreshException;
use League\OAuth2\Server\Exception\InvalidRequestException;

/**
 * Class OAuthExceptionHandler
 *
 * @package App\Exceptions
 */
class OAuthExceptionHandler
{

    /**
     * @param OAuthException $e
     * @return mixed
     */
    public function handle(OAuthException $e)
    {
        if (method_exists($this, camel_case($e->errorType))) {
            return $this->{camel_case($e->errorType)}($e);
        }
    }

    /**
     * @param OAuthException $e
     * @return mixed
     */
    protected function invalidRequest(OAuthException $e)
    {
        if ($e instanceof InvalidRequestException) {
            return Response::make(
                [
                    'errors' => [
                        'status' => '400',
                        'code' => 'InvalidRequest',
                        'title' => 'Invalid Request',
                        'detail' => 'The body does not have the necessary data to process the transaction',
                        'source' => [
                            'parameter' => $e->parameter
                        ]
                    ]
                ],
                400
            );
        }
        if ($e instanceof InvalidRefreshException) {
            return Response::make(
                [
                    'errors' => [
                        'status' => '400',
                        'code' => 'InvalidRefreshToken',
                        'title' => 'Invalid Refresh Token',
                        'detail' => 'The refresh token is invalid',
                        'source' => [
                            'parameter' => $e->parameter
                        ]
                    ]
                ],
                400
            );
        }
    }

    /**
     * @param OAuthException $e
     * @return mixed
     */
    protected function invalidCredentials(OAuthException $e)
    {
        return Response::make(
            [
                'errors' => [
                    'status' => '401',
                    'code' => 'InvalidCredentials',
                    'title' => 'Invalid Credentials',
                    'detail' => 'The information for the login is invalid'
                ]
            ],
            401
        );
    }

    /**
     * @param $e
     * @return mixed
     */
    protected function invalidClient($e)
    {
        return Response::make(
            [
                'errors' => [
                    'status' => '401',
                    'code' => 'InvalidClient',
                    'title' => 'Invalid Client',
                    'detail' => 'The client requesting the information is not registered in the API'
                ]
            ],
            401
        );
    }
}
