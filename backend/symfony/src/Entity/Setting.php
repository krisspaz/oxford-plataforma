<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SettingRepository::class)]
#[ApiResource]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['setting:read', 'setting:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['setting:read', 'setting:write'])]
    private ?string $value = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['setting:read', 'setting:write'])]
    private ?string $type = 'string'; // string, boolean, integer, json

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['setting:read', 'setting:write'])]
    private ?string $section = 'general';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(?string $section): static
    {
        $this->section = $section;
        return $this;
    }
}
