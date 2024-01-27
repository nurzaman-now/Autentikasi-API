<?php

namespace App\Exceptions;

use App\Helpers\ResponseFormatter;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    // public function register(): void
    // {
    //     $this->reportable(function (Throwable $e) {
    //         //
    //     });
    // }

    public function render($request, Throwable $e)
    {
        if ($request->is('api/*')) {
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return ResponseFormatter::responseError(message: 'Anda tidak memiliki izin untuk mengakses resource ini.', code: Response::HTTP_UNAUTHORIZED);
            } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                return ResponseFormatter::validatorError($e->errors());
            }
            return ResponseFormatter::responseError(message: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return parent::render($request, $e);
    }
}
