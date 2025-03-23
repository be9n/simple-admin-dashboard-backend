<?php

namespace App\Http\Traits;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseTrait
{
    use ApiResponseHelpers;

    /**
     * Return a success response.
     * 
     * @param string $message
     * @param ?array<string> $data
     * @return JsonResponse
     */
    public function successResponse(string $message = 'Processed Successfully', ?array $data = []): JsonResponse
    {
        return $this->apiResponse([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Return an unauthenticated (401) response.
     * 
     * @param string $message
     * @param ?array<string> $data
     * @return JsonResponse
     */
    public function respondUnAuthenticated(string $message = 'Unauthenticated!', ?array $data = []): JsonResponse
    {
        return $this->failResponse(message: $message, data: $data, code: Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Return an unauthenticated (401) response.
     * 
     * @param string $message
     * @param ?array<string> $data
     * @return JsonResponse
     */
    public function respondForbidden(string $message = 'Forbidden!', ?array $data = []): JsonResponse
    {
        return $this->failResponse(message: $message, data: $data, code: Response::HTTP_FORBIDDEN);
    }

    /**
     * Return a not found (404) response.
     * 
     * @param string $message
     * @param ?array<string> $data
     * @return JsonResponse
     */
    public function respondNotFound(string $message = 'Not Found', ?array $data = []): JsonResponse
    {
        return $this->failResponse(message: $message, data: $data, code: Response::HTTP_NOT_FOUND);
    }

    public function failResponse(string $message = 'Something went wrong, please try again.', ?array $errors = [], ?array $data = [], int $code = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->apiResponse([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
            'data' => $data,
        ], $code);
    }
}