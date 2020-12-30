<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler.
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @codeCoverageIgnore
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'status_code' => 401,
            ], 401);
        }
        if ($exception instanceof BodyTooLargeException) {
            return response()->json([
                'message' => 'The body is too large',
                'status_code' => 413,
            ], 413);
        }
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => $exception->getMessage(),
                'status_code' => 422,
                'errors' => $exception->errors(),
            ], 422);
        }
        if ($exception instanceof StoreResourceFailedException) {
            return response()->json([
                'message' => $exception->getMessage(),
                'status_code' => 422,
                'errors' => $exception->errors,
            ], 422);
        }
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => '404 Not Found',
                'status_code' => 404,
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
