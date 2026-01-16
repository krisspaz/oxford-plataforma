<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Teacher;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TeacherProcessor implements ProcessorInterface
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
        if ($data instanceof Teacher && $operation->getMethod() === 'POST') {
            // Check if user already exists
            $email = $data->getEmail();
            if ($email) {
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
                
                if (!$existingUser) {
                    // Create new User
                    $user = new User();
                    $user->setEmail($email);
                    $user->setName($data->getFirstName() . ' ' . $data->getLastName());
                    $user->setRoles([User::ROLE_DOCENTE]);
                    // Set default strong password or generate one
                    $plainPassword = 'oxford_teacher'; // In production, send email with set password link
                    $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                    
                    $this->entityManager->persist($user);
                    $data->setUser($user);
                } else {
                    // Link existing user
                    $data->setUser($existingUser);
                    // Ensure they have the role
                    if (!in_array(User::ROLE_DOCENTE, $existingUser->getRoles())) {
                        $roles = $existingUser->getRoles();
                        $roles[] = User::ROLE_DOCENTE;
                        $existingUser->setRoles(array_unique($roles));
                    }
                }
            }
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
