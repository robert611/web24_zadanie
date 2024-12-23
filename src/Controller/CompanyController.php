<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Route('/api/company')]
final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyRepository $companyRepository,
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
}
