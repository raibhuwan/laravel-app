<?php

namespace App\Exceptions;

use Exception;
use Google_Service_Exception;
use LogicException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Laravel\Passport\Exceptions\MissingScopeException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;

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
     * @param  \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // This will replace our 404 response with
        // a JSON response.
        if ($exception instanceof NotFoundHttpException && $request->wantsJson()) {
            return response()->json([
                'data' => 'Resource not found'
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException && $request->wantsJson()) {

            return response()->json([
                'data' => 'Method Not Allowed'
            ], 405);
        }

        if ($exception instanceof AuthenticationException && $request->wantsJson()) {
            return response()->json([
                'data' => 'The user is Unauthenticated'
            ], 401);
        }

        if ($exception instanceof MissingScopeException && $request->wantsJson()) {
            return response()->json([
                'data' => 'Invalid Scope'
            ], 403);
        }

        if ($exception instanceof AuthorizationException && $request->wantsJson()) {
            return response()->json([
                'data' => 'Insufficient privileges to perform this action'
            ], 403);
        }

        if ($exception instanceof Google_Service_Exception && $request->wantsJson()) {
            $message = json_decode($exception->getMessage());
            Log::useFiles(storage_path() . '/logs/googlePubSub.log');
            return response()->json([
                'data' => $message->error
            ], $exception->getCode());
        }

        if ($exception instanceof LogicException && $request->wantsJson()) {
            return response()->json([
                'data' => $exception->getMessage()
            ]);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
