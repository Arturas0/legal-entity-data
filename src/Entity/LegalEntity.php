<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\LegalEntityRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LegalEntityRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/legal-entities/{id}'),
        new GetCollection(uriTemplate: '/legal-entities'),
        new Post(),
    ],
    normalizationContext: ['groups' => ['read']]
)]
class LegalEntity
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id;

    #[ORM\Column(type: Types::BIGINT), Groups(['read'])]
    private ?string $code = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    private ?string $display_address = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    public ?\DateTimeImmutable $registeredAt = null;

    #[ORM\Column, Groups(['read'])]
    private ?int $type_code = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    private ?string $type_name = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    private ?string $status_code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDisplayAddress(): ?string
    {
        return $this->display_address;
    }

    public function setDisplayAddress(?string $display_address): void
    {
        $this->display_address = $display_address;
    }

    public function getRegisteredAt(): ?DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(?DateTimeImmutable $registeredAt): void
    {
        $this->registeredAt = $registeredAt;
    }

    public function getTypeCode(): ?int
    {
        return $this->type_code;
    }

    public function setTypeCode(?int $type_code): void
    {
        $this->type_code = $type_code;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function setTypeName(?string $type_name): void
    {
        $this->type_name = $type_name;
    }

    public function getStatusCode(): ?string
    {
        return $this->status_code;
    }

    public function setStatusCode(?string $status_code): void
    {
        $this->status_code = $status_code;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }
}
