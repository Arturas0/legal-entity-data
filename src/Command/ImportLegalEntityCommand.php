<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ImportLegalEntityService;
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
    description: 'Imports entities from url (https://www.registrucentras.lt) or from local file'
)]
class ImportLegalEntityCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ImportLegalEntityService $legalEntitiesService,
        private readonly string $activeEntities,
        private readonly string $inactiveEntities,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Legal entities import');

        try {
            $output->writeln("Starting to fetch active entities: $this->activeEntities");
            $activeEntitiesCsv = $this->getCsvContentBySpecifier($this->activeEntities);

            $output->writeln("Starting to fetch inactive entities: $this->inactiveEntities".PHP_EOL);
            $inactiveEntitiesCsv = $this->getCsvContentBySpecifier($this->inactiveEntities);

            $output->writeln("Content fetched successfully. Processing...");

            $this->legalEntitiesService->handleActiveEntities($activeEntitiesCsv, $io, $this->activeEntities);
            $this->legalEntitiesService->handleInactiveEntities($inactiveEntitiesCsv, $io, $this->inactiveEntities);
        } catch (UnavailableStream|InvalidArgument|Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getCsvContentBySpecifier(string $path): Reader
    {
        return str_starts_with($path, 'http')
            ? Reader::createFromString($this->client->request('GET', $path)->getContent())
            : Reader::createFromPath($this->projectDir.'/import/'.$path);
    }
}