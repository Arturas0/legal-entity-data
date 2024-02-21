<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait KernelTestTrait
{
    public function getEntityManager(): EntityManagerInterface
    {

        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        return $em;
    }

    /** @var Fixture[] $fixtures */
    protected function loadFixtures(array $fixtures): void
    {
        $loader = new Loader();
        $executor = new ORMExecutor($this->getEntityManager(), new ORMPurger());

        foreach ($fixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        $executor->execute($loader->getFixtures());
    }
}