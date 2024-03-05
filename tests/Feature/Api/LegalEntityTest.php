<?php

declare(strict_types=1);

namespace App\Tests\Feature\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\LegalEntityFixtures;
use App\DataFixtures\LegalEntityStatusFixtures;
use App\DataFixtures\LegalEntityTypeFixtures;
use App\Entity\LegalEntity;
use App\Tests\KernelTestTrait;

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

    public function test_can_filter_legal_entity_by_active_or_not(): void
    {
        $this->loadFixtures([
            new LegalEntityTypeFixtures(),
            new LegalEntityStatusFixtures(),
            new LegalEntityFixtures($this->getEntityManager()),
        ]);

        $this->createClient()->request('GET', 'api/legal-entities?active=true');

        $this->assertResponseIsSuccessful();

        $this->assertEquals(2, count($this->getEntityManager()->getRepository(LegalEntity::class)->getActive()));
    }
}
