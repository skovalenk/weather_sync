<?php

namespace Monogo\Weather\Spi;

interface PersistenceOperationInterface
{
    public const WEATHER_TABLE_NAME = 'monogo_weather_log';

    /**
     * Resource operation. For example, operation with database:
     * - create
     * - update
     * - read
     *
     * We don`t have delete operation. As usually there are no delete operations for logs
     *
     * Operations.
     * Each operation is performing it`s own DQL/DML queries to SQL.
     * Can be replaced with non-sql adapter
     *
     * @param array $operationalData
     * @return mixed
     */
    public function execute(array $operationalData);
}
