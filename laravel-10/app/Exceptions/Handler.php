<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Jobs\Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Str;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        NotFoundHttpException::class,
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
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Throwable $exception
     * @return void
     * @throws \Throwable
     */
    public function report(\Throwable $exception)
    {
        if(isset($_SERVER['REMOTE_ADDR'])) {
            $log = array(
                'body' => 'IP: ' . $_SERVER['REMOTE_ADDR'].'<br/>'.url()->full().'<br/>' . $exception,
            );
        
            $data = [
                'log' => $log
            ];

            if(!Str::contains($exception, 'NotFoundHttpException')) {
                Exception::dispatch($data);
            }
            
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function render($request, \Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            if (\Route::is('wizard-survey-store') || \Route::is('wizard-survey-update')) {
                return response()->json(['message' => __('messages.fix_survey_error'), 'errors' => $exception->validator->getMessageBag()], 422);
            } else {
                return response()->json(['message' => __('messages.fix_errors'), 'errors' => $exception->validator->getMessageBag()], 422);
            }
        }
        if ($request->is('api/*') || $request->is('mobile/*') || $request->is('registration/*') || $request->is('organizer/*') || $request->is('event/*')) {
            if ($exception instanceof HttpException) {
                return response()->json(['status' => 0, 'message' => $exception->getMessage() !== "" ? $exception->getMessage() : "Some Unknown Http Error",], $exception->getStatusCode());
            }
        }
        return parent::render($request, $exception);
    }
}
