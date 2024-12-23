<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Shared\FormHelper;
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

#[Route('/api/company')]
final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyRepository $companyRepository,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/index', name: 'company_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns companies list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Company::class, groups: ['company_read']))
        ),
    )]
    public function index(): Response
    {
        $companies = $this->companyRepository->findAll();

        return new JsonResponse($companies);
    }

    #[Route('/new', name: 'company_new', methods: ['POST'])]
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
            return new JsonResponse('Payload cannot be empty', Response::HTTP_BAD_REQUEST);
        }

        try {
            $requestBody = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return new JsonResponse('Invalid json payload', Response::HTTP_BAD_REQUEST);
        }

        $name = $requestBody['name'] ?? null;
        $nip = $requestBody['nip'] ?? null;
        $address = $requestBody['address'] ?? null;
        $city = $requestBody['city'] ?? null;
        $zipCode = $requestBody['zipCode'] ?? null;

        if (empty($name)) {
            return new JsonResponse('Name cannot be null or empty', Response::HTTP_BAD_REQUEST);
        }

        if (empty($nip)) {
            return new JsonResponse('Nip cannot be null or empty', Response::HTTP_BAD_REQUEST);
        }

        if (empty($address)) {
            return new JsonResponse('Address cannot be null or empty', Response::HTTP_BAD_REQUEST);
        }

        if (empty($city)) {
            return new JsonResponse('City cannot be null or empty', Response::HTTP_BAD_REQUEST);
        }

        if (empty($zipCode)) {
            return new JsonResponse('Zip code cannot be null or empty', Response::HTTP_BAD_REQUEST);
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
            return new JsonResponse(FormHelper::mapValidationErrorsToPlainString($errors), Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return new JsonResponse($company, Response::HTTP_CREATED);
    }
}
