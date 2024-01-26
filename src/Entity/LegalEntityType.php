<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\LegalEntityTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LegalEntityTypeRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
    ]
)]
class LegalEntityType
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true), Groups(['read'])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column, Groups(['read'])]
    #[Assert\NotNull]
    private ?int $code = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    #[Assert\NotBlank]
    private ?string $short_name = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    #[Assert\NotBlank]
    private ?string $long_name = null;

    #[ORM\Column(length: 20), Groups(['read'])]
    #[Assert\NotBlank]
    private ?string $type = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getShortName(): ?string
    {
        return $this->short_name;
    }

    public function setShortName(string $short_name): static
    {
        $this->short_name = $short_name;

        return $this;
    }

    public function getLongName(): ?string
    {
        return $this->long_name;
    }

    public function setLongName(string $long_name): static
    {
        $this->long_name = $long_name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
