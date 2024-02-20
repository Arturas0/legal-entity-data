<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\LegalEntityType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LegalEntityTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $arrayLegalEntityTypes = [
                ['code' => 310, 'short_name' => 'Uždaroji akcinė bendrovė', 'long_name' => 'Uždaroji akcinė bendrovė', 'type' => 'Privatus'],
                ['code' => 320, 'short_name' => 'Akcinė bendrovė', 'long_name' => 'Akcinė bendrovė', 'type' => 'Privatus'],
                ['code' => 571, 'short_name' => 'Viešosios įstaigos filialas', 'long_name' => 'Viešosios įstaigos filialas', 'type' => 'Viešasis'],
        ];

        foreach ($arrayLegalEntityTypes as $arrayLegalStatus) {
            $legalEntityStatus = new LegalEntityType();

            $legalEntityStatus
                ->setCode($arrayLegalStatus['code'])
                ->setShortName($arrayLegalStatus['short_name'])
                ->setLongName($arrayLegalStatus['long_name'])
                ->setType($arrayLegalStatus['type']);

            $manager->persist($legalEntityStatus);
        }

        $manager->flush();
    }
}
