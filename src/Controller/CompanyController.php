<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Shared\ValidationHelper;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/companies')]
final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyRepository $companyRepository,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'company_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns companies list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Company::class, groups: ['company_read']))
        ),
    )]
    public function list(): Response
    {
        $companies = $this->companyRepository->findAll();

        return new JsonResponse($companies, Response::HTTP_OK);
    }

    #[Route('', name: 'company_new', methods: ['POST'])]
    #[OA\Post(
        description: 'Creates a new company',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'nip', type: 'string'),
                    new OA\Property(property: 'address', type: 'string'),
                    new OA\Property(property: 'city', type: 'string'),
                    new OA\Property(property: 'zipCode', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created company',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Company::class, groups: ['company_read']))
                ),
            ),
        ],
    )]
    public function new(Request $request): Response
    {
        if (empty($request->getContent())) {
            return $this->formatBadRequestResponse('Payload cannot be empty');
        }

        try {
            $requestBody = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->formatBadRequestResponse('Invalid json payload');
        }

        $name = $requestBody['name'] ?? null;
        $nip = $requestBody['nip'] ?? null;
        $address = $requestBody['address'] ?? null;
        $city = $requestBody['city'] ?? null;
        $zipCode = $requestBody['zipCode'] ?? null;

        if (empty($name)) {
            return $this->formatBadRequestResponse('Name cannot be null or empty');
        }

        if (empty($nip)) {
            return $this->formatBadRequestResponse('Nip cannot be null or empty');
        }

        if (empty($address)) {
            return $this->formatBadRequestResponse('Address cannot be null or empty');
        }

        if (empty($city)) {
            return $this->formatBadRequestResponse('City cannot be null or empty');
        }

        if (empty($zipCode)) {
            return $this->formatBadRequestResponse('Zip code cannot be null or empty');
        }

        $company = Company::create(
            $name,
            $nip,
            $address,
            $city,
            $zipCode,
        );

        $errors = $this->validator->validate($company);

        if ($errors->count() > 0) {
            return $this->formatBadRequestResponse(ValidationHelper::mapValidationErrorsToPlainString($errors));
        }

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return new JsonResponse($company, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'company_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns single company',
        content: new OA\JsonContent(
            ref: new Model(type: Company::class, groups: ['company_read'])
        ),
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Company id',
        in: 'path',
        schema: new OA\Schema(type: 'string')
    )]
    public function show(int $id): Response
    {
        $company = $this->companyRepository->find($id);

        if (null === $company) {
            return $this->formatCompanyNotFoundResponse();
        }

        return new JsonResponse($company, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'company_edit', methods: ['PUT'])]
    #[OA\Put(
        description: 'Updates company',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'nip', type: 'string'),
                    new OA\Property(property: 'address', type: 'string'),
                    new OA\Property(property: 'city', type: 'string'),
                    new OA\Property(property: 'zipCode', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated company',
                content: new OA\JsonContent(
                    ref: new Model(type: Company::class, groups: ['company_read'])
                ),
            ),
        ],
    )]
    public function edit(int $id, Request $request): Response
    {
        $company = $this->companyRepository->find($id);

        if (null === $company) {
            return $this->formatCompanyNotFoundResponse();
        }

        if (empty($request->getContent())) {
            return $this->formatBadRequestResponse('Payload cannot be empty');
        }

        try {
            $requestBody = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->formatBadRequestResponse('Invalid json payload');
        }

        $name = $requestBody['name'] ?? null;
        $nip = $requestBody['nip'] ?? null;
        $address = $requestBody['address'] ?? null;
        $city = $requestBody['city'] ?? null;
        $zipCode = $requestBody['zipCode'] ?? null;

        if (empty($name)) {
            return $this->formatBadRequestResponse('Name cannot be null or empty');
        }

        if (empty($nip)) {
            return $this->formatBadRequestResponse('Nip cannot be null or empty');
        }

        if (empty($address)) {
            return $this->formatBadRequestResponse('Address cannot be null or empty');
        }

        if (empty($city)) {
            return $this->formatBadRequestResponse('City cannot be null or empty');
        }

        if (empty($zipCode)) {
            return $this->formatBadRequestResponse('Zip code cannot be null or empty');
        }

        $company->update(
            $name,
            $nip,
            $address,
            $city,
            $zipCode,
        );

        $errors = $this->validator->validate($company);

        if ($errors->count() > 0) {
            return $this->formatBadRequestResponse(ValidationHelper::mapValidationErrorsToPlainString($errors));
        }

        $this->entityManager->flush();

        return new JsonResponse($company, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'company_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: 'Deletes company',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Company id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
            ),
        ],
    )]
    public function delete(int $id): Response
    {
        $company = $this->companyRepository->find($id);

        if (null === $company) {
            return $this->formatCompanyNotFoundResponse();
        }

        $this->entityManager->remove($company);
        $this->entityManager->flush();

        return new JsonResponse('Company removed', Response::HTTP_NO_CONTENT);
    }

    public function formatBadRequestResponse(string $message): JsonResponse
    {
        return new JsonResponse([
            'developerMessage' => $message,
            'userMessage' => $message,
            'errorCode' => Response::HTTP_BAD_REQUEST,
            'moreInfo' => 'Please look into api/doc for more information.'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function formatCompanyNotFoundResponse(): JsonResponse
    {
        return new JsonResponse([
            'developerMessage' => 'Company not found',
            'userMessage' => 'Company not found',
            'errorCode' => Response::HTTP_NOT_FOUND,
            'moreInfo' => 'Please look into api/doc for more information.'
        ], Response::HTTP_NOT_FOUND);
    }
}
