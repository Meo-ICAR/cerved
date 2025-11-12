<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class BaseApiController extends BaseController
{
    /**
     * Send a success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($data = null, $message = null, $code = 200)
    {
        $response = [
            'success' => true,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        if (!is_null($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Send an error response.
     *
     * @param  string  $message
     * @param  array  $errors
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendError($message = null, $errors = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
