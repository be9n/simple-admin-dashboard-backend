<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $exception) {
            if ($exception instanceof NotFoundHttpException) {
                return response()->json(
                    [
                        'success' => false,
                        'code' => Response::HTTP_NOT_FOUND,
                        'message' => __('Not Found!'),
                        'data' => [],
                        'errors' => [],
                    ],
                    Response::HTTP_NOT_FOUND
                );
            } elseif ($exception instanceof ValidationException) {
                return response()->json(
                    [
                        'success' => false,
                        'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => __('Data are not valid, please check the error messages'),
                        'data' => [],
                        'errors' => $exception->validator->getMessageBag()
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            } elseif ($exception instanceof AuthenticationException) {
                return response()->json(
                    [
                        'success' => false,
                        'code' => Response::HTTP_UNAUTHORIZED,
                        'message' => __('Unauthenticated!'),
                        'data' => [],
                        'errors' => []
                    ],
                    Response::HTTP_UNAUTHORIZED
                );
            } elseif ($exception instanceof ModelNotFoundException) {
                return response()->json(
                    [
                        'success' => false,
                        'code' => Response::HTTP_NOT_FOUND,
                        'message' => __('Model Not Found Exception!'),
                        'data' => [],
                        'errors' => [],
                    ],
                    $this->setStatusCode(Response::HTTP_NOT_FOUND)
                );
            } elseif ($exception instanceof UnauthorizedException) {
                return response(
                    [
                        'success' => false,
                        'code' => Response::HTTP_FORBIDDEN,
                        'message' => __('Forbidden!'),
                        'data' => [],
                        'errors' => []
                    ],
                    Response::HTTP_FORBIDDEN
                );
            } else {
                return response()->json(
                    [
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                        'message' => __('Sorry, Something went wrong, try again later!'),
                        'success' => false,
                        'data' => [],
                        'errors' => $exception->getMessage(),
                        'trace' => request()->ip() == '127.0.0.1' ? $exception->getTrace() : null,
                    ],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        });
    })->create();
