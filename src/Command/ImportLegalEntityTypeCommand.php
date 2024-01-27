<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\LegalEntityType;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import-entity-type',
    description: 'Imports entity types from https://www.registrucentras.lt'
)]
class ImportLegalEntityTypeCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientInterface $client,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $path = 'https://www.registrucentras.lt/aduomenys/?byla=JAR_TEI_FORM_KLASIFIKATORIUS.csv';
            $csv = Reader::createFromString($this->client->request('GET', $path)->getContent());

            $csv->setHeaderOffset(0);
            $csv->setDelimiter('|');

            if (count($csv->getHeader()) === 1) {
                throw new \Exception("Invalid csv file: $path ");
            }

            $legalEntityTypeRepository = $this->entityManager->getRepository(LegalEntityType::class);

            $legalEntityTypes = $csv->getRecords();
            $newEntityTypeCount = 0;

            foreach ($legalEntityTypes as $legalEntityType) {
                $entityType = $legalEntityTypeRepository->findOneBy([
                    'code' => $legalEntityType['form_kodas'],
                ]);

                if (! $entityType) {
                    $this->createLegalEntityType($legalEntityType);

                    $newEntityTypeCount++;
                }
            }

            $this->entityManager->flush();
            $this->entityManager->clear();

            $io = new SymfonyStyle($input, $output);

            $message = $newEntityTypeCount > 0
                ? 'Newly created records: ' . $newEntityTypeCount . '.'
                : 'No new entries created.';

            $io->success("Entity types import done. $message");
        } catch (UnavailableStream|InvalidArgument|Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

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