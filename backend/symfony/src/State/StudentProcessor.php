<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Student;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StudentProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Student && $operation->getMethod() === 'POST') {
            // Check if user already exists
            $email = $data->getEmail();
            // Fallback if no email provided (students sometimes use username/carnet)
            if (!$email && $data->getCarnet()) {
                $email = $data->getCarnet() . '@oxford.edu';
                $data->setEmail($email);
            }

            if ($email) {
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                
                if (!$existingUser) {
                    // Create new User
                    $user = new User();
                    $user->setEmail($email);
                    $user->setName($data->getFirstName() . ' ' . $data->getLastName());
                    $user->setRoles([User::ROLE_ALUMNO]);
                    // Set default strong password or generate one
                    $plainPassword = 'oxford_student'; 
                    $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                    
                    $this->entityManager->persist($user);
                } else {
                    // User exists, ensure they have the role
                    if (!in_array(User::ROLE_ALUMNO, $existingUser->getRoles())) {
                        $roles = $existingUser->getRoles();
                        $roles[] = User::ROLE_ALUMNO;
                        $existingUser->setRoles(array_unique($roles));
                    }
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
