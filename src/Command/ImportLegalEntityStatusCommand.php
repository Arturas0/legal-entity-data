<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\LegalEntityStatus;
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
    name: 'app:import-entity-status',
    description: 'Imports entity statuses from https://www.registrucentras.lt'
)]
class ImportLegalEntityStatusCommand extends Command
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
            $path = 'https://www.registrucentras.lt/aduomenys/?byla=JAR_TEI_STATUSU_KLASIFIKATORIUS.csv';
            $csv = Reader::createFromString($this->client->request('GET', $path)->getContent());

            $csv->setHeaderOffset(0);
            $csv->setDelimiter('|');

            if (count($csv->getHeader()) === 1) {
                throw new \Exception("Invalid csv file: $path ");
            }

            $legalEntityStatusRepository = $this->entityManager->getRepository(LegalEntityStatus::class);

            $legalEntityStatuses = $csv->getRecords();
            $newEntityStatusCount = 0;

            foreach ($legalEntityStatuses as $legalEntityStatus) {
                $status = $legalEntityStatusRepository->findOneBy([
                    'code' => $legalEntityStatus['stat_kodas'],
                ]);

                if (! $status) {
                    $this->createLegalEntityStatus($legalEntityStatus);

                    $newEntityStatusCount++;
                }
            }

            $this->entityManager->flush();
            $this->entityManager->clear();

            $io = new SymfonyStyle($input, $output);

            $message = $newEntityStatusCount > 0
                ? 'Newly created records: ' . $newEntityStatusCount . '.'
                : 'No new entries created.';

            $io->success("Entity status import done. $message");
        } catch (UnavailableStream|InvalidArgument|Exception $e) {
            $output->writeln($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function createLegalEntityStatus(array $legalEntityStatus): void
    {
        $newEntityStatus = new LegalEntityStatus();
        $newEntityStatus->setCode((int) $legalEntityStatus['stat_kodas']);
        $newEntityStatus->setName($legalEntityStatus['stat_pavadinimas']);

        $this->entityManager->persist($newEntityStatus);
    }
}
