<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\LegalEntityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:import-entity-type',
    description: 'Imports entity types from https://www.registrucentras.lt'
)]
class ImportLegalEntityTypeCommand extends Command
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $decoder = new Serializer([new ObjectNormalizer], [new CsvEncoder]);

        $legalEntityTypes = $decoder->decode(file_get_contents(
            'https://www.registrucentras.lt/aduomenys/?byla=JAR_TEI_FORM_KLASIFIKATORIUS.csv'
        ),
            'csv',
            [CsvEncoder::DELIMITER_KEY => '|']
        );

        $legalEntityTypeRepository = $this->entityManager->getRepository(LegalEntityType::class);

        $newEntityTypeCount = 0;

        foreach ($legalEntityTypes as $legalEntityType) {
            $entityType = $legalEntityTypeRepository->findOneBy(['code' => $legalEntityType['form_kodas']]);

            if (! $entityType) {
                $this->createLegalEntityType($legalEntityType);

                $newEntityTypeCount++;
            }
        }

        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);

        $message = $newEntityTypeCount > 0
            ? 'Newly created records: '.$newEntityTypeCount.'.'
            : 'No new entries created.';

        $io->success("Entity types import done. $message");

        return Command::SUCCESS;
    }

    private function createLegalEntityType(array $legalEntityType): void
    {
        $newEntityType = new LegalEntityType();
        $newEntityType->setCode((int) $legalEntityType['form_kodas']);
        $newEntityType->setShortName($legalEntityType['form_pavadinimas']);
        $newEntityType->setLongName($legalEntityType['form_pav_ilgas']);
        $newEntityType->setType($legalEntityType['tipas']);

        $this->entityManager->persist($newEntityType);
    }
}