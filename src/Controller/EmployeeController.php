<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Resolver\EmployeeDTOResolver;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route('/api/employees')]
final class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly EmployeeRepository $employeeRepository,
        private readonly EntityManagerInterface $entityManager,
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

    #[Route('', name: 'employee_new', methods: ['POST'], format: 'json')]
    #[OA\Post(
        description: 'Creates a new employee',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'company', type: 'integer'),
                    new OA\Property(property: 'firstName', type: 'string'),
                    new OA\Property(property: 'lastName', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'phoneNumber', type: 'string'),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created employee',
                content: new OA\JsonContent(
                    ref: new Model(type: Employee::class, groups: ['employee_read'])
                ),
            ),
        ],
    )]
    public function new(Request $request, EmployeeDTOResolver $employeeDTOResolver): Response
    {
        if ($jsonResponse = $employeeDTOResolver->hasInvalidPayload($request)) {
            return $jsonResponse;
        }

        $employeeDTO = $employeeDTOResolver->resolve($request);

        $company = $employeeDTO->getCompany();

        $employee = Employee::create(
            $company,
            $employeeDTO->getFirstName(),
            $employeeDTO->getLastName(),
            $employeeDTO->getEmail(),
            $employeeDTO->getPhoneNumber(),
        );

        $company->addEmployee($employee);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        return new JsonResponse($employee, Response::HTTP_CREATED);
    }
}
