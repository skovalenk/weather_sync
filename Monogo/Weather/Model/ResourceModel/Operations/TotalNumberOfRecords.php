<?php

namespace Monogo\Weather\Model\ResourceModel\Operations;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\Expression;
use Monogo\Weather\Spi\PersistenceOperationInterface;

class TotalNumberOfRecords implements PersistenceOperationInterface
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Calculates the total number of records in the weather log table
     *
     * @param array $operationalData
     * @return mixed|void
     */
    public function execute(array $operationalData = [])
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                $connection->getTableName(self::WEATHER_TABLE_NAME),
                new Expression('COUNT(*)')
            );

        return $connection->fetchOne($select);
    }
}
