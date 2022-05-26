<?php

namespace Monogo\Weather\Model\ResourceModel\Operations;

use Magento\Framework\App\ResourceConnection;
use Monogo\Weather\Spi\PersistenceOperationInterface;

class Read implements PersistenceOperationInterface
{
    public const LIMIT = 'limit';
    public const PAGE_OFFSET = 'offset';

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
        //If there is no custom limit, let`s use 1, as a limit to extract one record
        $limit = $operationalData[self::LIMIT] ?? 1;
        unset($operationalData[self::LIMIT]);
        $page = $operationalData[self::PAGE_OFFSET] ?? 1;
        unset($operationalData[self::PAGE_OFFSET]);

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName(self::WEATHER_TABLE_NAME));

        //Please use only indexed fields here
        foreach ($operationalData as $condition => $value) {
            $select->where($condition . '= ?', $value);
        }
        $select->limitPage($page, $limit);
        //We are sorting by primary key desc in order to retrieve the last record
        $select->order('weather_id DESC');
        //All our read operations should always retrieve one record
        return $connection->fetchAssoc($select);
    }
}
