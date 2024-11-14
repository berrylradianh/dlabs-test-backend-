<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ResponseHelper
{
    private static $statusMessages = [
        HttpResponse::HTTP_OK => 'OK',
        HttpResponse::HTTP_CREATED => 'Created',
        HttpResponse::HTTP_NO_CONTENT => 'No Content',
        HttpResponse::HTTP_BAD_REQUEST => 'Bad Request',
        HttpResponse::HTTP_UNAUTHORIZED => 'Unauthorized',
        HttpResponse::HTTP_FORBIDDEN => 'Forbidden',
        HttpResponse::HTTP_NOT_FOUND => 'Not Found',
        HttpResponse::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        HttpResponse::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        HttpResponse::HTTP_SERVICE_UNAVAILABLE => 'Service Unavailable',
    ];

    public static function errorResponse(int $code, $errors = null): JsonResponse
    {
        return response()->json([
            'message' => self::getStatusMessage($code),
            'status' => $code,
            'errors' => $errors,
        ], $code);
    }

    public static function pagedResponse($data, int $page, int $size, int $total): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'page' => $page,
            'size' => $size,
            'total' => $total,
        ], HttpResponse::HTTP_OK);
    }

    public static function nonPagedResponse($data, int $code): JsonResponse
    {
        return response()->json([
            'data' => $data,
        ], $code);
    }

    private static function getStatusMessage(int $code): string
    {
        return self::$statusMessages[$code] ?? 'Unknown Status';
    }
}
