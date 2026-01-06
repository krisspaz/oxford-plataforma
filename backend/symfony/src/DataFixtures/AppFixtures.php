<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Subject;
use App\Entity\Bimester;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 1. Create Bimesters
        $bimesters = [];
        foreach (['I Bimestre', 'II Bimestre', 'III Bimestre', 'IV Bimestre'] as $i => $name) {
            $b = new Bimester();
            $b->setName($name);
            $b->setStartDate(new \DateTime(sprintf('2026-%02d-01', ($i * 2) + 1)));
            $b->setEndDate(new \DateTime(sprintf('2026-%02d-28', ($i * 2) + 2)));
            $b->setIsClosed($i === 0); // Close first one
            $manager->persist($b);
            $bimesters[] = $b;
        }

        // 2. Create Grades & Sections
        $grades = [];
        $sections = [];
        foreach (['1ro Primaria', '2do Primaria', '3ero Básico', '4to Bachillerato', '5to Bachillerato'] as $name) {
            $g = new Grade();
            $g->setName($name);
            $manager->persist($g);
            $grades[] = $g;
        }

        foreach (['A', 'B', 'C'] as $name) {
            $s = new Section();
            $s->setName($name);
            $manager->persist($s);
            $sections[] = $s;
        }

        // 3. Create Users
        // Admin
        $admin = new User();
        $admin->setEmail('admin@oxford.edu');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstName('Admin');
        $admin->setLastName('Principal');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'oxford123'));
        $manager->persist($admin);

        // Teachers (5)
        for ($i = 0; $i < 5; $i++) {
            $u = new User();
            $u->setEmail("teacher$i@oxford.edu");
            $u->setRoles(['ROLE_TEACHER']);
            $u->setFirstName("Profe");
            $u->setLastName("Doe $i");
            $u->setPassword($this->passwordHasher->hashPassword($u, 'teacher123'));
            $manager->persist($u);

            $t = new Teacher();
            $t->setUser($u);
            $t->setCode("DOC-00$i");
            $t->setSpecialization("Materia $i");
            $manager->persist($t);
        }

        // Students (50)
        for ($i = 0; $i < 50; $i++) {
            $s = new Student();
            $s->setFirstName("Estudiante");
            $s->setLastName("Apellido $i");
            $s->setCode(sprintf("EST-2026-%03d", $i));
            $s->setGrade($grades[$i % count($grades)]);
            $s->setSection($sections[$i % count($sections)]);
            $s->setStatus('active');
            $s->setBirthDate(new \DateTime('2010-01-01'));
            $manager->persist($s);
        }

        $manager->flush();
    }
}
