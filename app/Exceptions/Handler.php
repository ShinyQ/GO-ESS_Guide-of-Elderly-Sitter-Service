<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Api;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof MethodNotAllowedHttpException) {
            $code = 405;
            return Api::apiRespond($code, []);
        }   else if ($e instanceof NotFoundHttpException){
            $code = 404;
            return Api::apiRespond($code, []);
        }  else if ($e instanceof NotFoundHttpException) {
            $code = 500;
            return Api::apiRespond($code, $e);
        }
        return parent::render($request, $e);
    }
}
