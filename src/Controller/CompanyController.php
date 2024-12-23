<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/company')]
final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyRepository $companyRepository,
    ) {
    }

    #[Route('/index', name: 'company_index', methods: ['GET'])]
    public function index(): Response
    {
        $companies = $this->companyRepository->findAll();

        return new JsonResponse($companies);
    }
}
