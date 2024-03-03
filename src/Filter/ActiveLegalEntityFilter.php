<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class ActiveLegalEntityFilter extends AbstractFilter
{
    protected function filterProperty(
        string $property,
        mixed $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if ($property !== 'is_active') {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $parameterName = array_key_first($this->getProperties());

        if ($value === 'true') {
            $queryBuilder->andWhere(sprintf('%s.%s IS NULL', $alias, $parameterName));
        } else {
            $queryBuilder->andWhere(sprintf('%s.%s IS NOT NULL', $alias, $parameterName));
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'is_active' => [
                'property' => null,
                'type' => 'bool',
                'required' => false,
                'swagger' => [
                    'description' => 'Filter active or inactive legal entities.',
                    'name' => 'Active legal entities',
                ],
            ],
        ];
    }
}