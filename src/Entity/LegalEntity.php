<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Filter\ActiveLegalEntityFilter;
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
    ],
    normalizationContext: ['groups' => ['read']],
),
    ApiFilter(
        searchFilter::class,
        properties: [
            'code' => SearchFilterInterface::STRATEGY_START,
            'name' => SearchFilterInterface::STRATEGY_PARTIAL,
            'legalEntityStatus.code' => SearchFilterInterface::STRATEGY_EXACT,
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

    #[ORM\Column(length: 255, unique: true), Groups(['read'])]
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

    #[ORM\Column(type: 'datetime_immutable', nullable: true), Groups(['read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotNull]
    #[ApiFilter(ActiveLegalEntityFilter::class)]
    public ?\DateTimeImmutable $deregisteredAt = null;

    #[ORM\ManyToOne(targetEntity: 'LegalEntityType'), Groups(['read'])]
    private ?LegalEntityType $legalEntityType = null;
    #[ORM\ManyToOne(targetEntity: 'LegalEntityStatus'), Groups(['read'])]
    private ?LegalEntityStatus $legalEntityStatus = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $checksum = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDisplayAddress(): ?string
    {
        return $this->display_address;
    }

    public function setDisplayAddress(?string $display_address): self
    {
        $this->display_address = $display_address;

        return $this;
    }

    public function getRegisteredAt(): ?DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(?DateTimeImmutable $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    public function getDeregisteredAt(): ?DateTimeImmutable
    {
        return $this->deregisteredAt;
    }

    public function setDeregisteredAt(?DateTimeImmutable $deregisteredAt): self
    {
        $this->deregisteredAt = $deregisteredAt;

        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLegalEntityStatus(): ?LegalEntityStatus
    {
        return $this->legalEntityStatus;
    }

    public function setLegalEntityStatus(?LegalEntityStatus $legalEntityStatus): self
    {
        $this->legalEntityStatus = $legalEntityStatus;

        return $this;
    }

    public function getLegalEntityType(): ?LegalEntityType
    {
        return $this->legalEntityType;
    }

    public function setLegalEntityType(?LegalEntityType $legalEntityType): self
    {
        $this->legalEntityType = $legalEntityType;

        return $this;
    }

    public function getChecksum(): ?string
    {
        return $this->checksum;
    }

    public function setChecksum(): self
    {
        $this->checksum = hash('xxh128', implode(',', [
            'code' => $this->code,
            'name' => $this->name,
            'address' => $this->display_address,
            'entity_type_code' => $this->legalEntityType?->getCode(),
            'entity_status_code' => $this->legalEntityStatus?->getCode(),
        ]));

        return $this;
    }
}
