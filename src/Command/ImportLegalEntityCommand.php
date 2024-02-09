<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ImportLegalEntitiesService;
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
    name: 'app:import-entity',
    description: 'Imports entities from https://www.registrucentras.lt'
)]
class ImportLegalEntityCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ImportLegalEntitiesService $legalEntitiesService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Legal entities import');

        try {
            $path = 'https://www.registrucentras.lt/aduomenys/?byla=JAR_IREGISTRUOTI.csv';
            $output->writeln("Starting to download CSV file: $path");

            $csv = Reader::createFromString($this->client->request('GET', $path)->getContent());

            $output->writeln("File downloaded successfully. Processing...");

            $this->legalEntitiesService->import($csv, $io, $path);
        } catch (UnavailableStream|InvalidArgument|Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}