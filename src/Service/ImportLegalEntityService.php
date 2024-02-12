<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\LegalEntityDTO;
use App\Entity\LegalEntity;
use App\Entity\LegalEntityStatus;
use App\Entity\LegalEntityType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\SyntaxError;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class ImportLegalEntityService
{
    private int $newEntityCount = 0;
    private int $updatedEntityCount = 0;
    private int $batchSize = 100;

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Stopwatch $stopwatch,
    ) {
    }

    /**
     * @throws InvalidArgument
     * @throws SyntaxError
     * @throws Exception
     */
    public function handleActiveEntities(Reader $csv, SymfonyStyle $io, string $path): void
    {
        $csv->setHeaderOffset(0);
        $csv->setDelimiter('|');

        if (count($csv->getHeader()) === 1) {
            throw new Exception("Invalid CSV file: $path");
        }

        $recordCount = $csv->count();

        $section = 'processing_csv_file';
        $this->stopwatch->start($section);
        $io->progressStart($recordCount);

        $legalEntities = $csv->getRecords();
        $legalEntityRepository = $this->entityManager->getRepository(LegalEntity::class);

        foreach ($legalEntities as $legalEntity) {
            $legalEntityDTO = new LegalEntityDTO(
                code: $legalEntity['ja_kodas'],
                name: $legalEntity['ja_pavadinimas'],
                display_address: $legalEntity['adresas'],
                registered_at: DateTimeImmutable::createFromFormat('Y-m-d', $legalEntity['ja_reg_data']),
                legal_entity_type_code: (int) $legalEntity['form_kodas'],
                legal_entity_status_code: (int) $legalEntity['stat_kodas'],
            );

            $entity = $legalEntityRepository->findOneBy([
                'code' => $legalEntityDTO->code,
            ]);

            if (! $entity) {
                $this->createLegalEntity($legalEntityDTO);
                $this->newEntityCount++;

                if (($this->newEntityCount % $this->batchSize) === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            }

            if ($entity) {
                $checksumCalculatedFromSource = hash('xxh128', implode(',', [
                    'code' => $legalEntityDTO->code,
                    'name' => $legalEntityDTO->name,
                    'address' => $legalEntityDTO->display_address,
                    'entity_type_code' => $legalEntityDTO->legal_entity_type_code,
                    'entity_status_code' => $legalEntityDTO->legal_entity_status_code,
                ]));

                if (! hash_equals($checksumCalculatedFromSource, $entity->getChecksum())) {
                    $this->updateLegalEntity($entity, $legalEntityDTO);
                    $this->updatedEntityCount++;

                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }

                $this->entityManager->detach($entity);
            }

            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $io->progressFinish();

        $this->printImportResultMessage($io, true);
        $this->stopwatch->stop($section);
        $io->writeln((string)$this->stopwatch->getEvent($section));
    }

    public function handleInactiveEntities(Reader $csv, SymfonyStyle $io, string $path): void
    {
        $csv->setHeaderOffset(0);
        $csv->setDelimiter('|');

        if (count($csv->getHeader()) === 1) {
            throw new Exception("Invalid CSV file: $path");
        }

        $this->newEntityCount = 0;
        $this->updatedEntityCount = 0;

        $recordCount = $csv->count();

        $section = 'processing_csv_file';
        $this->stopwatch->start($section);
        $io->progressStart($recordCount);

        $legalEntities = $csv->getRecords();
        $legalEntityRepository = $this->entityManager->getRepository(LegalEntity::class);

        $statusDeregistered = $this->entityManager->getRepository(LegalEntityStatus::class)
            ->findOneBy(['name' => 'Išregistruotas']);

        foreach ($legalEntities as $arrayLegalEntity) {
            $legalEntityDTO = new LegalEntityDTO(
                code: $arrayLegalEntity['ja_kodas'],
                name: $arrayLegalEntity['ja_pavadinimas'],
                display_address: $arrayLegalEntity['adresas'],
                registered_at: DateTimeImmutable::createFromFormat('Y-m-d', $arrayLegalEntity['ja_reg_data']),
                legal_entity_type_code: (int) $arrayLegalEntity['form_kodas'],
                legal_entity_status_code: $statusDeregistered->getCode(),
                deregistered_at: DateTimeImmutable::createFromFormat('Y-m-d', $arrayLegalEntity['isreg_data']),
            );

            $entity = $legalEntityRepository->findOneBy(['code' => $legalEntityDTO->code]);

            if (! $entity) {
                $this->createLegalEntity($legalEntityDTO);
                $this->newEntityCount++;

                if (($this->newEntityCount % $this->batchSize) === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            }

            if ($entity) {
                $checksumCalculatedFromSource = hash('xxh128', implode(',', [
                    'code' => $legalEntityDTO->code,
                    'name' => $legalEntityDTO->name,
                    'address' => $legalEntityDTO->display_address,
                    'entity_type_code' => $legalEntityDTO->legal_entity_type_code,
                    'entity_status_code' => $legalEntityDTO->legal_entity_status_code,
                ]));

                if (! hash_equals($checksumCalculatedFromSource, $entity->getChecksum())) {
                    $this->updateLegalEntity($entity, $legalEntityDTO);
                    $this->updatedEntityCount++;

                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }

                $this->entityManager->detach($entity);
            }

            $io->progressAdvance();
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $io->progressFinish();

        $this->printImportResultMessage($io, false);
        $this->stopwatch->stop($section);
        $io->writeln((string)$this->stopwatch->getEvent($section));
    }

    private function createLegalEntity(LegalEntityDTO $legalEntityDTO): void
    {
        $legalEntity = new LegalEntity();

        $legalEntity->setCode($legalEntityDTO->code)
            ->setName($legalEntityDTO->name)
            ->setDisplayAddress($legalEntityDTO->display_address)
            ->setRegisteredAt($legalEntityDTO->registered_at)
            ->setDeregisteredAt($legalEntityDTO->deregistered_at)
            ->setLegalEntityType($this->getEntityType($legalEntityDTO->legal_entity_type_code))
            ->setLegalEntityStatus($this->getEntityStatus($legalEntityDTO->legal_entity_status_code))
            ->setChecksum();

        $this->entityManager->persist($legalEntity);
    }

    private function updateLegalEntity(
        LegalEntity $legalEntity,
        LegalEntityDTO $legalEntityDTO,
    ): void {
        $legalEntity->setCode($legalEntityDTO->code)
            ->setName($legalEntityDTO->name)
            ->setDisplayAddress($legalEntityDTO->display_address)
            ->setRegisteredAt($legalEntityDTO->registered_at)
            ->setDeregisteredAt($legalEntityDTO->deregistered_at)
            ->setLegalEntityType($this->getEntityType($legalEntityDTO->legal_entity_type_code))
            ->setLegalEntityStatus($this->getEntityStatus($legalEntityDTO->legal_entity_status_code))
            ->setChecksum();

        $this->entityManager->persist($legalEntity);
    }

    private function getEntityType(int $code): ?LegalEntityType
    {
        return $this->entityManager->getRepository(LegalEntityType::class)
            ->findOneBy([
                'code' => $code,
            ]);
    }

    private function getEntityStatus(int $code): ?LegalEntityStatus
    {
        return $this->entityManager->getRepository(LegalEntityStatus::class)
            ->findOneBy([
                'code' => $code,
            ]);
    }

    private function printImportResultMessage(SymfonyStyle $io, bool $proccessingActiveEntities): void
    {
        $message = '';

        if ($this->newEntityCount > 0) {
            $message = "Newly created records: $this->newEntityCount.";
        }

        if ($this->updatedEntityCount > 0) {
            $recordsUpdatedMessage = "Updated records: $this->updatedEntityCount.";

            $message = $message
                ? $message . PHP_EOL . $recordsUpdatedMessage
                : $recordsUpdatedMessage;
        }

        $message = $message ?: 'No entry created or updated.';

        $proccessingActiveEntities
            ? $io->success("Active entity import done. $message")
            : $io->success("Inactive entity import done. $message");
    }
}