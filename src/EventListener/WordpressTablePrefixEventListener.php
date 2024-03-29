<?php

declare(strict_types=1);

namespace Sword\SwordBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sword\SwordBundle\Entity\WordpressEntityInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsDoctrineListener(event: Events::loadClassMetadata)]
final class WordpressTablePrefixEventListener
{
    public function __construct(
        #[Autowire('%sword.table_prefix%')] private readonly string $prefix
    ) {
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();

        if ($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
            return;
        }

        if (
            $classMetadata->getReflectionClass()
            && $classMetadata->getReflectionClass()
                ->implementsInterface(WordpressEntityInterface::class)
        ) {
            $classMetadata->setPrimaryTable([
                'name' => $this->prefix . $classMetadata->getTableName()
            ]);

            foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
                if ($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY) {
                    $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                    $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
                }
            }
        }
    }
}
