<?php
/**
 * Расширение «префиксы имён таблиц» для Doctrine
 *
 * @link http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/sql-table-prefixes.html
 */

namespace Eresus\ORM\Extensions;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Расширение «префиксы имён таблиц» для Doctrine
 *
 * @since 3.01
 */
class TablePrefix
{
    /**
     * Префикс
     * @var string
     */
    protected $prefix = '';

    /**
     * Конструктор расширения
     *
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = strval($prefix);
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $classMetadata->setTableName($this->prefix . $classMetadata->getTableName());
        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping)
        {
            if (ClassMetadataInfo::MANY_TO_MANY == $mapping['type'])
            {
                $mappedTableName
                    = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name']
                    = $this->prefix . $mappedTableName;
            }
        }
    }
}

