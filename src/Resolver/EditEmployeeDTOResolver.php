<?php

declare(strict_types=1);

namespace App\Resolver;

use App\DTO\EditEmployeeDTO;
use App\Shared\RequestResponseHelper;
use App\Shared\ValidationHelper;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EditEmployeeDTOResolver
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function hasInvalidPayload(Request $request): JsonResponse|null
    {
        if (empty($request->getContent())) {
            return RequestResponseHelper::formatBadRequestResponse('Payload cannot be empty');
        }

        try {
            $requestBody = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return RequestResponseHelper::formatBadRequestResponse('Invalid json payload');
        }

        $employeeDTO = EditEmployeeDTO::create(
            $requestBody['firstName'] ?? null,
            $requestBody['lastName'] ?? null,
            $requestBody['email'] ?? null,
            $requestBody['phoneNumber'] ?? null,
        );

        $errors = $this->validator->validate($employeeDTO);
        if (count($errors) > 0) {
            return RequestResponseHelper::formatBadRequestResponse(
                ValidationHelper::mapValidationErrorsToPlainArray($errors),
            );
        }

        return null;
    }

    public function resolve(Request $request): EditEmployeeDTO
    {
        $requestBody = json_decode($request->getContent(), true);

        return EditEmployeeDTO::create(
            $requestBody['firstName'],
            $requestBody['lastName'],
            $requestBody['email'],
            $requestBody['phoneNumber'] ?? null,
        );
    }
}
