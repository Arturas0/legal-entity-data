<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\LegalEntityFixtures;
use App\DataFixtures\LegalEntityStatusFixtures;
use App\DataFixtures\LegalEntityTypeFixtures;
use App\Entity\LegalEntity;
use App\Tests\KernelTestTrait;
use Doctrine\ORM\Mapping\ClassMetadata;

class LegalEntityTest extends ApiTestCase
{
    use KernelTestTrait;

    public function testGetLegalEntityCollection(): void
    {
        $this->loadFixtures([
            new LegalEntityTypeFixtures(),
            new LegalEntityStatusFixtures(),
            new LegalEntityFixtures($this->getEntityManager()),
        ]);

         $this->createClient()->request('GET', 'api/legal-entities');

         $this->assertResponseIsSuccessful();

        $this->assertEquals(3, $this->getEntityManager()->getRepository(LegalEntity::class)->count());
    }


}