<?php

namespace Monogo\Weather\Spi;

interface WeatherProviderInterface
{
    /**
     * Makes API request to weather service, and retrieves weather for specific city in a specific country.
     * Country code should be passed in ISO2 format. For example, PL, UA, etc
     * City should be passed in free format.
     *
     * @param string $countryCode
     * @param string $city
     * @return float
     */
    public function get(string $countryCode, string $city): float;
}
