<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class Handler
 * @package App\Exceptions
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof NotFoundHttpException){
            return $this->renderNotFoundException($exception);
        }
        if($exception instanceof ModelNotFoundException) {
            return $this->renderModelNotFoundException($exception);
        }
        if($exception instanceof AccessDeniedHttpException) {
            return $this->renderAccessDeniedException($exception);
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        $errors = $e->validator->errors()->getMessages();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $errors
            ], 422);
        }

        return redirect()->back()->withInput(
            $request->input()
        )->withErrors($errors);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function renderNotFoundException($e)
    {
        if(request()->expectsJson()){
            return response()->json(['message' => 'Not Found'], 404);
        }
        return $this->renderHttpException($e);
    }

    /**
     * @param $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function renderModelNotFoundException($e)
    {
        if(request()->expectsJson()){
            return response()->json(['message' => 'Not Found'], 404);
        }
        throw new NotFoundHttpException();
    }

    /**
     * @param $e
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function renderAccessDeniedException($e)
    {
        if(request()->expectsJson()){
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $this->renderHttpException($e);
    }
}
