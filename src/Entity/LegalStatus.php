<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LegalStatusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LegalStatusRepository::class)]
#[ApiResource]
class LegalStatus
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true), Groups(['read'])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column, Groups(['read'])]
    private ?int $status_code = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    private ?string $status_name = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getStatusCode(): ?int
    {
        return $this->status_code;
    }

    public function setStatusCode(int $status_code): static
    {
        $this->status_code = $status_code;

        return $this;
    }

    public function getStatusName(): ?string
    {
        return $this->status_name;
    }

    public function setStatusName(string $status_name): static
    {
        $this->status_name = $status_name;

        return $this;
    }
}
