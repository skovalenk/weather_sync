<?php

namespace Monogo\Weather\Model\ResourceModel\Operations;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Sql\Expression;
use Monogo\Weather\Spi\PersistenceOperationInterface;

class Update implements PersistenceOperationInterface
{
    public const DEFAULT_BATCH_SIZE = 20000;
    public const COLUMN_TO_UPDATE = 'column';
    public const INCREMENT = 'increment';
    public const ID_FROM = 'id_from';

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Batch update of records
     *
     * @param array $operationalData
     * @return void
     */
    public function execute(array $operationalData): void
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->update(
            $connection->getTableName(self::WEATHER_TABLE_NAME),
            [
                $operationalData[self::COLUMN_TO_UPDATE] => new Expression(sprintf(
                    '%s + %f',
                    $operationalData[self::COLUMN_TO_UPDATE],
                    (float) $operationalData[self::INCREMENT]
                ))
            ],
            [
                'weather_id >= ?' => $operationalData[self::ID_FROM],
                'weather_id <= ?' => $operationalData[self::ID_FROM] + self::DEFAULT_BATCH_SIZE
            ]
        );
    }
}
