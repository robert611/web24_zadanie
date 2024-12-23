<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/employees')]
final class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
    ) {
    }

    #[Route('', name: 'employee_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns employees list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Employee::class, groups: ['employee_read']))
        ),
    )]
    public function list(): Response
    {
        $employees = $this->employeeRepository->findAll();

        return new JsonResponse($employees);
    }
}
