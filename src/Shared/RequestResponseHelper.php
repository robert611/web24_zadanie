<?php

declare(strict_types=1);

namespace App\Shared;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RequestResponseHelper
{
    /**
     * @param string|array<int, array{field: string, message: string}> $message
     */
    public static function formatBadRequestResponse(string|array $message): JsonResponse
    {
        if (is_string($message)) {
            $message = [['message' => $message]];
        }

        return new JsonResponse([
            'developerMessage' => $message,
            'userMessage' => $message,
            'errorCode' => Response::HTTP_BAD_REQUEST,
            'moreInfo' => 'Please look into api/doc for more information.'
        ], Response::HTTP_BAD_REQUEST);
    }
}
