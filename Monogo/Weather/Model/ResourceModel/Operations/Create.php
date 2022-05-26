<?php

namespace Monogo\Weather\Model\ResourceModel\Operations;

use Magento\Framework\App\ResourceConnection;
use Monogo\Weather\Spi\PersistenceOperationInterface;

class Create implements PersistenceOperationInterface
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
     * Inserts new weather into weather log
     *
     * @param array $operationalData
     * @return mixed|void
     */
    public function execute(array $operationalData)
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->insert(
            $connection->getTableName(self::WEATHER_TABLE_NAME),
            $operationalData
        );
    }
}
