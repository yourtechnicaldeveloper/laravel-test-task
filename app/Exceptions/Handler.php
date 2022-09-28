<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\ApiResponser;

class Handler extends ExceptionHandler {

    use ApiResponser;

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
    public function register() {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e) {
        if ($request->is('api/*')) {
            return $this->renderAPI($request, $e);
        }
        return parent::render($request, $e);
    }

    public function renderAPI($request, Throwable $e) {
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->error(401, "Someone is using your account in other device. If you do not identify this activity then reset your password from forgot password screen.");
        } else if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->error(404, "Resource not found.", $e);
        } else if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return $this->error(400, 'Endpoint not found.');
        } else if ($e instanceof \Illuminate\Validation\ValidationException) {
            $message = collect(collect($e->errors())->first())->first();
            $messageData = explode("##&&##", $message);
            if (count($messageData) > 1) {
                $status = intval($messageData[0]);
                $message = $messageData[1];
                return $this->error($status, $message);
            }
            return $this->validation_error($message);
        } else if ($e instanceof \Illuminate\Database\QueryException) {
            return $this->error(400, "Something went wrong while fetching data.", $e);
        } else if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return $this->error($e->getStatusCode(), exception_message($e), $e);
        } else if ($e instanceof \ErrorException) {
            return $this->error(400, exception_message($e), $e);
        } else if ($this->isHttpException($e) && $e->getStatusCode() == 404) {
            return $this->error(404, "API not found.");
        } else if ($e instanceof \Error) {
            return $this->error(500, exception_message($e));
        } else {
            return $this->error(500, "Something want wrong.", $e);
        }
    }

}
