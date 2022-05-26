<?php

namespace Monogo\Weather\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Metrics implements OptionSourceInterface
{
    //There are 2 main metrics used for different countries
    public const CELSIUS_METRIC = 'celsius';
    public const FAHRENHEIT_METRIC = 'fahrenheit';

    /**
     * @return \string[][]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => self::CELSIUS_METRIC,
                'value' => self::CELSIUS_METRIC
            ],
            [
                'label' => self::FAHRENHEIT_METRIC,
                'value' => self::FAHRENHEIT_METRIC
            ]
        ];
    }
}
