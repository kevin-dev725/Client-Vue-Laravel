<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    const
        RESPONSE_UNEXPECTED_ERROR = 'unexpected_error',
        RESPONSE_INVALID_CLIENT = 'invalid_client',
        RESPONSE_UNAUTHORIZED = 'unauthorized',
        RESPONSE_UNAUTHENTICATED = 'unauthenticated',
        RESPONSE_MODEL_NOT_FOUND = 'model_not_found',
        RESPONSE_UNPROCESSABLE_ENTITY = 'unprocessable_entity';

    /**
     * @param array|null $data
     * @return JsonResponse
     */
    public static function success(array $data = null)
    {
        if ($data == null) {
            $data = [];
        }

        return response()->json(array_merge(
            [
                'success' => true,
            ],
            $data
        ));
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public static function internalServerError(array $data)
    {
        return response()->json($data, 500);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public static function serviceUnavailable(array $data)
    {
        return response()->json($data, 503);
    }
}
