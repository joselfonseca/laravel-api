<?php

namespace App\Exceptions;

use Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class UnauthorizedExceptionHandler
 *
 * @package App\Exceptions
 */
class UnauthorizedExceptionHandler
{

    /**
     * @param UnauthorizedHttpException $exception
     * @return mixed
     */
    public function handle(UnauthorizedHttpException $exception)
    {
        return Response::make(
            [
                'errors' => [
                    'status' => '401',
                    'code' => 'AuthenticationFailed',
                    'title' => 'Authentication Failed',
                    'detail' => 'Failed to authenticate because of bad credentials or an invalid authorization header.',
                    'source' => [
                        'parameter' => 'Authentication-header'
                    ]
                ]
            ],
            401
        );
    }
}
