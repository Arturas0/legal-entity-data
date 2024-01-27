<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\LegalEntityRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LegalEntityRepository::class)]
#[ApiResource(
    operations: [
        new Get(uriTemplate: '/legal-entities/{id}'),
        new GetCollection(uriTemplate: '/legal-entities'),
        new Post(),
    ],
    normalizationContext: ['groups' => ['read']],
),
    ApiFilter(
        searchFilter::class,
        properties: [
            'code' => SearchFilter::STRATEGY_START,
            'name' => SearchFilter::STRATEGY_PARTIAL,
        ]
    )
]
class LegalEntity
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(length: 255), Groups(['read'])]
    #[Assert\NotBlank]
    #[ApiProperty(
        openapiContext: [
            'example' => '300020079'
        ]
    )]
    private ?string $code = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255), Groups(['read'])]
    #[Assert\NotBlank]
    private ?string $display_address = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull]
    public ?\DateTimeImmutable $registeredAt = null;

    #[ORM\ManyToOne(targetEntity: 'LegalEntityType'), Groups(['read'])]
    private ?LegalEntityType $legalEntityType = null;
    #[ORM\ManyToOne(targetEntity: 'LegalEntityStatus'), Groups(['read'])]
    private ?LegalEntityStatus $legalEntityStatus = null;

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

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLegalEntityStatus(): ?LegalEntityStatus
    {
        return $this->legalEntityStatus;
    }

    public function setLegalEntityStatus(?LegalEntityStatus $legalEntityStatus): void
    {
        $this->legalEntityStatus = $legalEntityStatus;
    }

    public function getLegalEntityType(): ?LegalEntityType
    {
        return $this->legalEntityType;
    }

    public function setLegalEntityType(?LegalEntityType $legalEntityType): void
    {
        $this->legalEntityType = $legalEntityType;
    }
}
