<?php

namespace App\DTO\Response;

use App\Entity\User;

/**
 * User Response DTO - Serialized user data for API responses
 */
class UserResponse
{
    public int $id;
    public string $email;
    public ?string $username;
    public array $roles;
    public bool $isActive;
    public bool $has2FA;
    public ?string $createdAt;

    public static function fromEntity(User $user): self
    {
        $dto = new self();
        $dto->id = $user->getId() ?? 0;
        $dto->email = $user->getEmail();
        $dto->username = $user->getUsername();
        $dto->roles = $user->getRoles();
        $dto->isActive = $user->isActive();
        $dto->has2FA = $user->getTwoFactorAuth()?->isEnabled() ?? false;
        $dto->createdAt = $user->getCreatedAt()?->format('Y-m-d H:i:s');
        return $dto;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'roles' => $this->roles,
            'isActive' => $this->isActive,
            'has2FA' => $this->has2FA,
            'createdAt' => $this->createdAt,
        ];
    }
}
