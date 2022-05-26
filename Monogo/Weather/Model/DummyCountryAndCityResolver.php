<?php

namespace Monogo\Weather\Model;

use Monogo\Weather\Spi\CountryAndCityResolverInterface;

/**
 * This is dummy class created for test task purposes.
 * Please change it to real one, that can calculates geolocation, when it would be used in production
 */
class DummyCountryAndCityResolver implements CountryAndCityResolverInterface
{
    /**
     * @return string
     */
    public function getCity(): string
    {
        return 'Lublin';
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return 'PL';
    }
}
