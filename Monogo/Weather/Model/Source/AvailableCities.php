<?php

namespace Monogo\Weather\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AvailableCities implements OptionSourceInterface
{
    public const CITY_COUNTRY_DELIMITER = '=';

    /**
     * This source provider, retrieves the most common cities and countries
     *
     * @return \string[][]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 'PL' . self::CITY_COUNTRY_DELIMITER . 'Lublin',
                'label' => 'Poland, Lublin'
            ],
            [
                'value' => 'PL' . self::CITY_COUNTRY_DELIMITER . 'Warsaw',
                'label' => 'Poland, Warsaw'
            ],
            [
                'value' => 'PL' . self::CITY_COUNTRY_DELIMITER . 'Krakow',
                'label' => 'Poland, Krakow'
            ],
            [
                'value' => 'PL' . self::CITY_COUNTRY_DELIMITER . 'Katowice',
                'label' => 'Poland, Katowice'
            ]
        ];
    }
}
