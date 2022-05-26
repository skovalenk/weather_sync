<?php

namespace Monogo\Weather\Spi;

interface CountryAndCityResolverInterface
{
    /**
     * Retrieve current country of the customer in ISO2 format
     *
     * @return string
     */
    public function getCountry(): string;

    /**
     * Retrieve current city of the customer
     *
     * @return string
     */
    public function getCity(): string;
}
