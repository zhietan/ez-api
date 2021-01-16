<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\RestApiValidationErrorException;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{

    protected $statusCode = 200;

    protected function getStatusCode()
    {
        return $this->statusCode;
    }

    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    protected function respond($data, $headers = [])
    {
        return response()->json($data, $this->statusCode, $headers);
    }

    protected function makeResponse($data = null, $message = null, $headers = [], $result = 'success')
    {
        $result = [
            'status' => $result,
            'status_code' => $this->statusCode,
        ];
        if(!empty($message)) $result['message'] = $message;
        $result['data'] = $data;

        return $this->respond($result, $headers);
    }

    protected function respondNotFound($message = 'Not Found!', $headers = [])
    {
        return $this->setStatusCode(404)->makeResponse(null, $message, $headers, 'error');
    }

    protected function respondUnauthorized($message = 'Unauthorized!', $headers = [])
    {
        return $this->setStatusCode(401)->makeResponse(null, $message, $headers, 'error');
    }

    protected function respondBadRequest($message = 'Not Found!', $headers = [])
    {
        return $this->setStatusCode(400)->makeResponse(null, $message, $headers, 'error');
    }

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validation = app('validator')->make($request->all(), $rules, $messages);

        //dd($validation->errors());

        if ($validation->fails()) $this->throwRestValidationError($validation->errors()->messages(), $validation->errors()->first());

        return true;
    }

    public function throwRestValidationError(Array $errors, $message)
    {
        $error = [
            'status_code' => 422,
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ];


        throw new RestApiValidationErrorException($error, $message);
    }

    protected function respondUnknownError(Exception $e, $message = 'Unknown Error! Process aborted.', $headers = [])
    {
        $message = $e->getMessage();

        Log::error("[Unknown Error] {$message}\r\nFile {$e->getFile()}:{$e->getLine()} with message {$e->getMessage()}\r\n{$e->getTraceAsString()}");
        report($e);

        if ($e instanceof ModelNotFoundException) throw $e;

        return $this->respondValidationError($message, $headers);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
        ], 200);
    }
}
