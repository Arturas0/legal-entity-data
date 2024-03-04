<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\LegalEntity;
use App\Entity\LegalEntityStatus;
use App\Entity\LegalEntityType;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class LegalEntityFixtures extends Fixture
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $arrayLegalEntities = [
            [
                'ja_kodas' => '110018332',
                'ja_pavadinimas' => 'Bendra Lietuvos-Rusijos įmonė "PETRONIKA"',
                'adresas' => 'Vilnius, Salomėjos Nėries g. 29-56, LT-06312',
                'ja_reg_data' => '1991-06-13',
                'isreg_data' => '2024-02-28',
                'form_kodas' => '310',
                'form_pavadinimas' => 'Uždaroji akcinė bendrovė',
                'stat_kodas' => '9',
                'stat_pavadinimas' => 'Inicijuojamas likvidavimas',
                'stat_data_nuo' => '2022-12-28',
                'formavimo_data' => '2024-01-01',
            ],
            [
                'ja_kodas' => '300642292',
                'ja_pavadinimas' => 'Viešosios įstaigos "Palaimos židinys" filialas',
                'adresas' => 'Jonavos r. sav., Rukla, Rupeikio g. 7-37, LT-55289',
                'ja_reg_data' => '2007-01-31',
                'isreg_data' => null,
                'form_kodas' => '571',
                'form_pavadinimas' => 'Viešosios įstaigos filialas',
                'stat_kodas' => '0',
                'stat_pavadinimas' => 'Teisinis stat neįregistruotas',
                'stat_data_nuo' => '2007-01-31',
                'formavimo_data' => '2024-01-01',
            ],
            [
                'ja_kodas' => '300643558',
                'ja_pavadinimas' => 'UAB "Kauno buitinė chemija"',
                'adresas' => 'Kaunas, Savanorių pr. 339A, LT-50119',
                'ja_reg_data' => '2007-02-05',
                'isreg_data' => null,
                'form_kodas' => '310',
                'form_pavadinimas' => 'Uždaroji akcinė bendrovė',
                'stat_kodas' => '1',
                'stat_pavadinimas' => 'Reorganizuojamas',
                'stat_data_nuo' => '2023-10-17',
                'formavimo_data' => '2024-01-01',
            ],
        ];

        foreach ($arrayLegalEntities as $arrayLegal) {
            $legalEntity = new LegalEntity();

            $deregistrationDate = $arrayLegal['isreg_data']
                ? DateTimeImmutable::createFromFormat('Y-m-d', $arrayLegal['isreg_data'])
                : null;

            $legalEntity
                ->setCode($arrayLegal['ja_kodas'])
                ->setName($arrayLegal['ja_pavadinimas'])
                ->setDisplayAddress($arrayLegal['adresas'])
                ->setRegisteredAt(DateTimeImmutable::createFromFormat('Y-m-d', $arrayLegal['ja_reg_data']))
                ->setDeregisteredAt($deregistrationDate)
                ->setLegalEntityType($this->getEntityType((int)$arrayLegal['form_kodas']))
                ->setLegalEntityStatus($this->getEntityStatus((int)$arrayLegal['stat_kodas']))
                ->setChecksum();

            $manager->persist($legalEntity);
        }

        $manager->flush();
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
}
