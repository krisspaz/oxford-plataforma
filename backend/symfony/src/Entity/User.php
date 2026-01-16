<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    processor: \App\State\UserPasswordHasher::class
)]
#[UniqueEntity(fields: ['email'], message: 'Este correo electrónico ya está registrado.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Roles del sistema
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_CONTABILIDAD = 'ROLE_CONTABILIDAD';
    public const ROLE_SECRETARIA = 'ROLE_SECRETARIA';
    public const ROLE_COORDINACION = 'ROLE_COORDINACION';
    public const ROLE_DIRECCION = 'ROLE_DIRECCION';
    public const ROLE_INFORMATICA = 'ROLE_INFORMATICA';
    public const ROLE_DOCENTE = 'ROLE_DOCENTE';
    public const ROLE_ALUMNO = 'ROLE_ALUMNO';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "El email es obligatorio")]
    #[Assert\Email(message: "El email no es válido")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "La contraseña es obligatoria")]
    #[Assert\Length(min: 8, minMessage: "La contraseña debe tener al menos 8 caracteres")]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Embedded(class: TwoFactorAuth::class)]
    private ?TwoFactorAuth $twoFactorAuth = null;

    public function getTwoFactorAuth(): ?TwoFactorAuth
    {
        return $this->twoFactorAuth;
    }

    public function setTwoFactorAuth(?TwoFactorAuth $twoFactorAuth): static
    {
        $this->twoFactorAuth = $twoFactorAuth;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }



    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function __construct()
    {
        $this->twoFactorAuth = new TwoFactorAuth();
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isDocente(): bool
    {
        return $this->hasRole(self::ROLE_DOCENTE);
    }

    public function isAlumno(): bool
    {
        return $this->hasRole(self::ROLE_ALUMNO);
    }

    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Administrador',
            self::ROLE_CONTABILIDAD => 'Contabilidad',
            self::ROLE_SECRETARIA => 'Secretaría',
            self::ROLE_COORDINACION => 'Coordinación',
            self::ROLE_DIRECCION => 'Dirección',
            self::ROLE_INFORMATICA => 'Informática',
            self::ROLE_DOCENTE => 'Docente',
            self::ROLE_ALUMNO => 'Alumno',
        ];
    }
}

