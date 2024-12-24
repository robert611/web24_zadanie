<?php

declare(strict_types=1);

namespace App\Resolver;

use App\DTO\EmployeeDTO;
use App\Repository\CompanyRepository;
use App\Shared\RequestResponseHelper;
use App\Shared\ValidationHelper;
use JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmployeeDTOResolver
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly CompanyRepository $companyRepository,
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

        $companyId = $requestBody['company'] ?? null;

        if (false === empty($companyId)) {
            $company = $this->companyRepository->find($companyId);
        } else {
            $company = null;
        }

        $employeeDTO = EmployeeDTO::create(
            $company,
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

    public function resolve(Request $request): EmployeeDTO
    {
        $requestBody = json_decode($request->getContent(), true);

        $company = $this->companyRepository->find($requestBody['company']);

        return EmployeeDTO::create(
            $company,
            $requestBody['firstName'],
            $requestBody['lastName'],
            $requestBody['email'],
            $requestBody['phoneNumber'] ?? null,
        );
    }
}
