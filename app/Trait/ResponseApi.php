<?php

namespace App\Trait;

use Illuminate\Http\Exceptions\HttpResponseException;

trait ResponseApi
{
    /**
     * Response api template convention
     *
     * @param  mixed  $message
     * @param  mixed  $data
     * @param  mixed  $statusCode
     * @param  mixed  $addheader  for additional key and value
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function returnResponseApi(bool|string $statusMessage = true, ?string $message = null, $data = null, ?int $statusCode = null, ?array $addheader = null)
    {
        $response = [
            'status' => $statusMessage,
            $statusMessage == false ? 'error' : 'message' => $message,
            'data' => ($data == '' || $data == null) ? [] : $data,
        ];

        // if ($data == null | $data == '') {
        //     unset($response['data']);
        // }

        if ($addheader !== null) {
            $response = array_merge($response, $addheader);
        }

        switch ($statusMessage) {
            case true:
                return response()->json($response, $statusCode);
            case false:
                throw new HttpResponseException(
                    response()->json($response, $statusCode)
                );
            default:
                throw new HttpResponseException(
                    response()->json([
                        'status' => false,
                        'message' => 'Method Parameter Violation, Input Parameter Must Be Follow the rules',
                    ], 403)
                );
        }
    }

    /**
     * if return  but the status message string
     * @param bool|string $statusMessage
     * @param mixed $message
     * @param mixed $data
     * @param mixed $statusCode
     * @param mixed $addheader
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function returnCustomResponseApi(bool|string $statusMessage = true, ?string $message = null, $data = null, ?int $statusCode = null, ?array $addheader = null, string $statusMessageKey = 'status' )
    {
        $response = [
            $statusMessageKey => $statusMessage,
            'message' => $message,
            'data' => ($data == '' || $data == null) ? [] : $data,
        ];

        if ($addheader !== null) {
            $response = array_merge($response, $addheader);
        }

        return response()->json($response, $statusCode);
    }
}
