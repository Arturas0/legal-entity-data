<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;

readonly class LegalEntityDTO
{
    public function __construct(
        public string $code,
        public string $name,
        public ?string $display_address,
        public ?DateTimeImmutable $registered_at,
        public ?int $legal_entity_type_code,
        public ?int $legal_entity_status_code,
        public ?DateTimeImmutable $deregistered_at = null,
    ) {
    }
}
