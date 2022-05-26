<?php

namespace Monogo\Weather\Api\Data;

interface WeatherInterface
{
    /**
     * Retrieves temperature
     *
     * @return float
     */
    public function getTemperature(): float;

    /**
     * Retrieves city
     *
     * @return string
     */
    public function getCity(): string;

    /**
     * Retrieves country code
     *
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * Current unix timestamp of weather
     *
     * @return int
     */
    public function getLoggedAt(): ?int;
}
