<?php

declare(strict_types=1);

namespace App\Tests\Console;

use App\Command\ImportLegalEntityCommand;
use App\DataFixtures\LegalEntityStatusFixtures;
use App\DataFixtures\LegalEntityTypeFixtures;
use App\Entity\LegalEntity;
use App\Tests\KernelTestTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ImportLegalEntityTest extends KernelTestCase
{
    use KernelTestTrait;

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $activeEntityData = [
            ['ja_kodas', 'ja_pavadinimas', 'adresas', 'ja_reg_data', 'form_kodas', 'form_pavadinimas', 'stat_kodas', 'stat_pavadinimas', 'stat_data_nuo', 'formavimo_data'],
            ['110018332', 'Bendra Lietuvos-Rusijos įmonė "PETRONIKA"', 'Vilnius, Salomėjos Nėries g. 29-56, LT-06312', '1991-06-13', '310', 'Uždaroji akcinė bendrovė', '9', 'Inicijuojamas likvidavimas', '2022-12-28', '2024-01-01'],
            ['300642292', 'Viešosios įstaigos "Palaimos židinys" filialas', 'Jonavos r. sav., Rukla, Rupeikio g. 7-37, LT-55289', '2007-01-31', '571', 'Viešosios įstaigos filialas', '0', 'Teisinis stat neįregistruotas', '2007-01-31', '2024-01-01'],
            ['300643558', 'UAB "Kauno buitinė chemija"', 'Kaunas, Savanorių pr. 339A, LT-50119', '2007-02-05', '310', 'Uždaroji akcinė bendrovė', '1', 'Reorganizuojamas', '2023-10-17', '2024-01-01'],
        ];

        $inactiveEntityData = [
            ['ja_kodas', 'ja_pavadinimas', 'adresas', 'ja_reg_data', 'form_kodas', 'form_pavadinimas', 'isreg_data', 'formavimo_data'],
            ['110003259', 'UAB "EurėjaTransport"', 'Vilnius, J. Basanavičiaus g. 29A, LT-03109', '1991-04-15', '310', 'Uždaroji akcinė bendrovė', '2023-09-08', '2024-02-01'],
            ['110006554', 'Akcinė bendrovė "LITHUN"', 'Vilnius, Žarijų g. 8, LT-02300', '1991-04-25', '320', 'Akcinė bendrovė', '2018-02-22', '2024-02-01'],
            ['110018332', 'Bendra Lietuvos-Rusijos įmonė "PETRONIKA"', 'Vilnius, Salomėjos Nėries g. 29-56, LT-06312', '1991-06-13', '310', 'Uždaroji akcinė bendrovė', '2024-01-11', '2024-02-01'],
        ];

        $activeEntitiesCsv = $this->generateCsvContent($activeEntityData);
        $inactiveEntitiesCsv = $this->generateCsvContent($inactiveEntityData);

        $httpClient = new MockHttpClient([
            'http://example.com/active.csv' => new MockResponse($activeEntitiesCsv),
            'http://example.com/inactive.csv' => new MockResponse($inactiveEntitiesCsv),
        ]);

        $importService = $container->get('App\Service\ImportLegalEntityService');
        $projectDir = $container->getParameter('kernel.project_dir');

        $command = new ImportLegalEntityCommand(
            $httpClient,
            $importService,
            'http://example.com/active.csv',
            'http://example.com/inactive.csv',
            $projectDir
        );

        $application = new Application(self::$kernel);
        $application->add($command);

        $this->commandTester = new CommandTester($command);
    }

    private function generateCsvContent(array $data): string
    {
        $csvContent = implode('|', $data[0]) . "\n";

        foreach (array_slice($data, 1) as $row) {
            $csvContent .= implode('|', $row) . "\n";
        }

        return $csvContent;
    }
    public function testCanImportActiveAndInactiveEntitiesFromUrl(): void
    {
        $this->loadFixtures([
            new LegalEntityTypeFixtures(),
            new LegalEntityStatusFixtures(),
        ]);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Active entity import done. Newly created records: 3.', $output);
        $this->assertStringContainsString('Inactive entity import done. Newly created records: 2', $output);
        $this->assertStringContainsString('Updated records: 1.', $output);

        $this->assertEquals(5, $this->getEntityManager()->getRepository(LegalEntity::class)->count([]));
    }
}