<?php

namespace Monogo\Weather\Console\Command;

use Monogo\Weather\Model\ResourceModel\Operations\LastAndFirstRecord;
use Monogo\Weather\Model\ResourceModel\Operations\Update;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * There is a conceptual problem:
 *
 * - Update of a table with 1 million records with TRANSACTION ISOLATION LEVEL `COMMITED`, but not `REPEATABLE READ`
 * can lead to locks and even dead locks, in case if we will try to update the whole table at once.
 * - Batch update is not still supported by MySQL (UPDATE .... LIMIT, OFFSET) because there is no order by in UPDATE
 * query
 * - However, we have an ID - weather_id, which is auto_increment. And we are not going to delete records from table.
 * That means that we can rely on weather_id.
 * For example,
 *
 * UPDATE .... WHERE weather_id < 20000;
 * UPDATE .... WHERE weather_id < 40000;
 * UPDATE .... WHERE weather_id < 60000;
 */
class TemperatureIncrement extends Command
{
    /**
     * @var Update
     */
    private Update $updateOperation;

    /**
     * @var LastAndFirstRecord
     */
    private LastAndFirstRecord $lastAndFirstRecord;

    /**
     * @param Update $updateOperation
     * @param LastAndFirstRecord $lastAndFirstRecord
     * @param string|null $name
     */
    public function __construct(
        Update $updateOperation,
        LastAndFirstRecord $lastAndFirstRecord,
        string $name = null
    ) {
        parent::__construct($name);
        $this->updateOperation = $updateOperation;
        $this->lastAndFirstRecord = $lastAndFirstRecord;
    }

    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('weather:temperature:increment');
        $this->addArgument(
            'increment',
            InputArgument::REQUIRED,
            'Increment value float or decimal. Increases all temperatures by this value'
        );
        $this->setDescription('Increment all temperatures in weather_log table by float value');
        parent::configure();
    }

    /**
     * As log table can be big enough we need to iterate and update batch by batch. Because:
     *
     * - DML operation on the whole table will lead to complete table lock often not only for write but for read operations
     * - BIG update will lead to high memory consumption on MySQL side
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $increment = (float) $input->getArgument('increment');
        list('first' => $firstId, 'last' => $lastId) = $this->lastAndFirstRecord->execute();
        //The total number of batches, that we need to update temperature in
        $batchNumber = ceil($lastId - $firstId / Update::DEFAULT_BATCH_SIZE);
        //We are iterating through all batches to update values
        for ($batchIndex = 0; $batchIndex <= $batchNumber; $batchIndex++) {
            $this->updateOperation->execute([
                Update::COLUMN_TO_UPDATE => 'value',
                Update::INCREMENT => $increment,
                Update::ID_FROM => $firstId + ($batchIndex * Update::DEFAULT_BATCH_SIZE)
            ]);
        }
    }
}
