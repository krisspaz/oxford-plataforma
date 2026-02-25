<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Entity\Teacher;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasher implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof User) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        if ($data->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $data,
                $data->getPassword()
            );
            $data->setPassword($hashedPassword);
        }

        // Set tenant if not already set (default to tenant 1)
        if ($data->getTenant() === null) {
            $tenant = $this->entityManager->getRepository(\App\Entity\Tenant::class)->find(1);
            if ($tenant) {
                $data->setTenant($tenant);
            }
        }

        // Persist the user first to get an ID
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        // Check if this is a new creation (POST operation) and auto-create profiles
        if (str_contains(strtolower($operation->getName() ?? ''), 'post') || str_contains(strtolower($operation->getMethod() ?? ''), 'post')) {
            $this->createLinkedProfile($data);
        }

        return $result;
    }

    private function createLinkedProfile(User $user): void
    {
        $roles = $user->getRoles();
        $tenant = $user->getTenant();

        // Check if Teacher profile already exists for this user
        if (in_array('ROLE_DOCENTE', $roles)) {
            $existingTeacher = $this->entityManager->getRepository(Teacher::class)
                ->findOneBy(['user' => $user]);
            
            if (!$existingTeacher) {
                $nameParts = explode(' ', $user->getName() ?? 'Nuevo Docente');
                $firstName = $nameParts[0] ?? 'Nuevo';
                $lastName = implode(' ', array_slice($nameParts, 1)) ?: 'Docente';

                $teacher = new Teacher();
                $teacher->setFirstName($firstName);
                $teacher->setLastName($lastName);
                $teacher->setEmail($user->getEmail());
                $teacher->setUser($user);
                $teacher->setIsActive(true);
                $teacher->setContractType('Tiempo Completo');
                $teacher->setHireDate(new \DateTime());
                if ($tenant) {
                    $teacher->setTenant($tenant);
                }

                $this->entityManager->persist($teacher);
                $this->entityManager->flush();
            }
        }

        // Check if Student profile already exists for this user
        if (in_array('ROLE_ALUMNO', $roles)) {
            $existingStudent = $this->entityManager->getRepository(Student::class)
                ->findOneBy(['user' => $user]);
            
            if (!$existingStudent) {
                $nameParts = explode(' ', $user->getName() ?? 'Nuevo Estudiante');
                $firstName = $nameParts[0] ?? 'Nuevo';
                $lastName = implode(' ', array_slice($nameParts, 1)) ?: 'Estudiante';

                $student = new Student();
                $student->setFirstName($firstName);
                $student->setLastName($lastName);
                $student->setEmail($user->getEmail());
                $student->setUser($user);
                $student->setIsActive(true);
                $student->setEnrollmentDate(new \DateTime());
                if ($tenant) {
                    $student->setTenant($tenant);
                }

                $this->entityManager->persist($student);
                $this->entityManager->flush();
            }
        }
    }
}
