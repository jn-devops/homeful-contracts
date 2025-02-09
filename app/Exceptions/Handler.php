<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types that should not be reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register any exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (UpdateContractContactException $e) {
            return response()->json([
                'error' => 'Contract contact update failed.',
                'message' => $e->getMessage(),
            ], 500);
        });
    }

    public function report(Throwable $e): void
    {
        // Log specific exceptions or send them to a monitoring service
        parent::report($e);
    }
}
