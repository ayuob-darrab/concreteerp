<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // معالجة تجاوز عدد المحاولات المسموح بها
        $this->renderable(function (ThrottleRequestsException $e, $request) {
            if ($request->is('login') || $request->routeIs('login')) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                return redirect()->route('login')
                    ->with('error', "⚠️ تم تجاوز عدد المحاولات المسموح بها. يرجى الانتظار {$retryAfter} ثانية قبل المحاولة مرة أخرى.");
            }
        });
    }
}
