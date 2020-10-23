<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException ;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * Report or log an exception.
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
   /* public function render($request, Exception $exception)
    {
       // return parent::render($request, $exception);
       if ($request->wantsJson()) {   //add Accept: application/json in request
        return $this->handleApiException($request, $exception);
    } else {
        $retval = parent::render($request, $exception);
    }

    return $retval;
    }
    private function handleApiException($request, Exception $exception)
{
    $exception = $this->prepareException($exception);

    if ($exception instanceof \Illuminate\Http\Exception\HttpResponseException) {
        $exception = $exception->getResponse();
    }

    if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
        $exception = $this->unauthenticated($request, $exception);
    }

    if ($exception instanceof \Illuminate\Validation\ValidationException) {
        $exception = $this->convertValidationExceptionToResponse($exception, $request);
    }

    return $this->customApiResponse($exception);
}
private function customApiResponse($exception)
{
    if (method_exists($exception, 'getStatusCode')) {
        $statusCode = $exception->getStatusCode();
    } else {
        $statusCode = 500;
    }

    $response = [];

    switch ($statusCode) {
        case 401:
            $response['message'] = 'Unauthorized';
            break;
        case 403:
            $response['message'] = 'Forbidden';
            break;
        case 404:
            $response['message'] = 'Not Found';
            break;
        case 405:
            $response['message'] = 'Method Not Allowed';
            break;
        case 422:
            $response['message'] = $exception->original['message'];
            $response['errors'] = $exception->original['errors'];
            break;
        default:
            $response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
            break;
    }

    if (config('app.debug')) {
        $response['trace'] = $exception->getTrace();
        $response['code'] = $exception->getCode();
    }

    $response['status'] = $statusCode;

    return response()->json($response, $statusCode);
}*/
public function render($request, Exception $exception)
{
   if($request->expectsJson())
    {
        if($exception instanceof ModelNotFoundException)
        {
            return response()->json([
                'error'=>'Model Not Found!!'
                
            ],Response :: HTTP_NOT_FOUND);
        }
        if($exception instanceof NotFoundHttpException)
        {
            return response()->json([
                'error'=>'Incorrect Route!!'
                
            ],Response :: HTTP_NOT_FOUND);
        }

    }
   
    return parent::render($request, $exception);
}
}
