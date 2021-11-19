<?php

namespace App\Modules\Common\ApiPlatform\Filter;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryBuilderHelper;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LogLevel;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Service\Attribute\Required;

class ExcludeFilter extends AbstractContextAwareFilter
{
    private IriConverterInterface $iriConverter;

    #[Required]
    public function setIriConverter(IriConverterInterface $iriConverter): void
    {
        $this->iriConverter = $iriConverter;
    }

    private function extractPropertyName(string $property): string
    {
        return str_replace('exclude_', '', $property);
    }

    protected function getIdFromValue(string $value): mixed
    {
        try {
            $item = $this->iriConverter->getItemFromIri($value, ['fetch_data' => false]);

            return PropertyAccess::createPropertyAccessor()->getValue($item, 'id');
        } catch (InvalidArgumentException $e) {
            $this->logger->log(LogLevel::ERROR, 'Invalid IRI specified', ['iri' => $value]);
        }

        return $value;
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $propertyName = $this->extractPropertyName($property);

        // otherwise filter is applied to order and page as well
        if (
            !str_contains($property, 'exclude_') ||
            !$this->isPropertyEnabled($propertyName, $resourceClass) ||
            !$this->isPropertyMapped($propertyName, $resourceClass, true)
        ) {
            return;
        }

        $values = is_array($value) ? $value : [$value];

        $alias = $queryBuilder->getRootAliases()[0];
        $metadata = $this->getNestedMetadata($resourceClass, []);

        if ($metadata->hasField($propertyName)) {
            if ($propertyName === 'id') {
                $values = array_map([$this, 'getIdFromValue'], $values);
            }

            $this->addConditions($queryBuilder, $queryNameGenerator, $alias, $propertyName, $values);
            return;
        }

        $values = array_map([$this, 'getIdFromValue'], $values);
        $associationFieldIdentifier = 'id';

        $associationAlias = $alias;
        $associationField = $propertyName;
        if (
            $metadata->isCollectionValuedAssociation($associationField) ||
            $metadata->isAssociationInverseSide($propertyName)
        ) {
            $associationAlias = QueryBuilderHelper::addJoinOnce(
                $queryBuilder,
                $queryNameGenerator,
                $alias,
                $associationField,
                Join::LEFT_JOIN
            );
            $associationField = $associationFieldIdentifier;
        }

        $this->addConditions($queryBuilder, $queryNameGenerator, $associationAlias, $associationField, $values);
    }

    private function addConditions(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $nameGenerator,
        string $alias,
        string $propertyName,
        array $values
    ): void {
        $valueParameter = ':'.$nameGenerator->generateParameterName($propertyName);
        $aliasedField = sprintf('%s.%s', $alias, $propertyName);

        if (1 === \count($values)) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->orX(
                    $queryBuilder->expr()->neq($aliasedField, $valueParameter),
                    $queryBuilder->expr()->isNull($aliasedField)
                ))
                ->setParameter($valueParameter, $values[0]);

            return;
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->notIn($aliasedField, $valueParameter),
                $queryBuilder->expr()->isNull($aliasedField)
            )
        )->setParameter($valueParameter, $aliasedField);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(string $resourceClass): array
    {
        $description = [];
        if (!empty($this->properties)) {
            foreach (array_keys($this->properties) as $property) {
                if (!$this->isPropertyMapped($property, $resourceClass, true)) {
                    continue;
                }
                if ($this->isPropertyNested($property, $resourceClass)) {
                    continue; // Do not support nested properties for now
                }

                $field = $property;
                $metadata = $this->getClassMetadata($resourceClass);
                $propertyName = $this->normalizePropertyName($property);

                if ($metadata->hasField($field)) {
                    $typeOfField = $this->getType($metadata->getTypeOfField($field));

                    $description[sprintf('%s_%s', 'exclude', $property)] = [
                        'property' => $propertyName,
                        'type' => $typeOfField,
                        'required' => false,
                    ];
                    $description[sprintf('%s_%s[]', 'exclude', $property)] = [
                        'property' => $propertyName,
                        'type' => $typeOfField,
                        'required' => false,
                    ];
                } elseif ($metadata->hasAssociation($field)) {
                    $description[sprintf('%s_%s', 'exclude', $property)] = [
                        'property' => $propertyName,
                        'type' => 'string',
                        'required' => false,
                    ];
                    $description[sprintf('%s_%s[]', 'exclude', $property)] = [
                        'property' => $propertyName,
                        'type' => 'string',
                        'required' => false,
                    ];
                }
            }
        }

        return $description;
    }

    protected function getType(string $doctrineType): string
    {
        return match ($doctrineType) {
            Types::ARRAY => 'array',
            Types::BIGINT, Types::INTEGER, Types::SMALLINT => 'int',
            Types::BOOLEAN => 'bool',
            Types::DATE_MUTABLE,
            Types::TIME_MUTABLE,
            Types::DATETIME_MUTABLE,
            Types::DATETIMETZ_MUTABLE,
            Types::DATE_IMMUTABLE,
            Types::TIME_IMMUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIMETZ_IMMUTABLE => \DateTimeInterface::class,
            Types::FLOAT => 'float',
            default => 'string',
        };
    }
}
