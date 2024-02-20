<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\LegalEntityStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LegalEntityStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $arrayLegalStatuses = [
            ['code' => 0, 'name' => 'Teisinis statusas neįregistruotas'],
            ['code' => 1, 'name' => 'Reorganizuojamas'],
            ['code' => 2, 'name' => 'Dalyvaujantis reorganizavime'],
            ['code' => 3, 'name' => 'Pertvarkomas'],
            ['code' => 4, 'name' => 'Restruktūrizuojamas'],
            ['code' => 5, 'name' => 'Bankrutuojantis'],
            ['code' => 6, 'name' => 'Bankrutavęs'],
            ['code' => 7, 'name' => 'Likviduojamas'],
            ['code' => 8, 'name' => 'Dalyvaujantis atskyrime'],
            ['code' => 9, 'name' => 'Inicijuojamas likvidavimas'],
            ['code' => 10, 'name' => 'Išregistruotas'],
            ['code' => 11, 'name' => 'Likviduotas'],
            ['code' => 12, 'name' => 'Nepersiregistravęs'],
            ['code' => 13, 'name' => 'Bankrutuojantis'],
            ['code' => 16, 'name' => 'Inicijuojantis Europos bendrovės steigimą jungimosi būdu'],
            ['code' => 17, 'name' => 'Inicijuojantis Europos bendrovės steigimą valdymo (holdingo) būdu'],
            ['code' => 18, 'name' => 'Europos bendrovė, kurios buveinė yra perkeliama'],
            ['code' => 19, 'name' => 'Inicijuojantis Europos kooperatinės bendrovės steigimą jungimosi būdu'],
            ['code' => 20, 'name' => 'Europos kooperatinė bendrovė, kurios buveinė perkeliama'],
            ['code' => 21, 'name' => 'Jungiama, peržengiant vienos valstybės ribas, akcinė bendrovė ar uždaroji akcinė bendrovė'],
            ['code' => 22, 'name' => 'Dalyvaujanti vienos valstybės ribas peržengiančiame jungimesi AB ar UAB'],
            ['code' => 23, 'name' => 'Jungiamas peržengiant vienos valstybės ribas juridinis asmuo'],
            ['code' => 24, 'name' => 'Dalyvaujantis jungimesi peržengiant vienos valstybės ribas juridinis asmuo'],
            ['code' => 25, 'name' => 'Perkeliantis buveinę'],
            ['code' => 26, 'name' => 'Likviduojamas dėl bankroto'],
            ['code' => 27, 'name' => 'Jungiama peržengiant vienos valstybės ribas bendrovė'],
            ['code' => 28, 'name' => 'Dalyvaujanti vienos valstybės ribas peržengiančiame jungimesi bendrovė'],
            ['code' => 29, 'name' => 'Pertvarkoma peržengiant vienos valstybės ribas bendrovė'],
            ['code' => 30, 'name' => 'Skaidoma peržengiant vienos valstybės ribas bendrovė'],
        ];

        foreach ($arrayLegalStatuses as $arrayLegalStatus) {
            $legalEntityStatus = new LegalEntityStatus();

            $legalEntityStatus
                ->setCode($arrayLegalStatus['code'])
                ->setName($arrayLegalStatus['name']);

            $manager->persist($legalEntityStatus);
        }

        $manager->flush();
    }
}
