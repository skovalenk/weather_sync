<?php

namespace Monogo\Weather\Model;

use Monogo\Weather\Api\Data\WeatherInterface;

/**
 * This is how real, immutable DTO should looks like, not like corrupted one that is used in Magento.
 */
class Weather implements WeatherInterface
{
    /**
     * @var float
     */
    private float $temperature;

    /**
     * @var string
     */
    private string $countryCode;

    /**
     * @var ?int
     */
    private ?int $loggedAt;

    /**
     * @var string
     */
    private string $city;

    /**
     * @param float $temperature
     * @param string $countryCode
     * @param string $city
     * @param ?int $loggedAt
     */
    public function __construct(
        float $temperature,
        string $countryCode,
        string $city,
        ?int $loggedAt = null
    ) {
        $this->temperature = $temperature;
        $this->countryCode = $countryCode;
        $this->city = $city;
        $this->loggedAt = $loggedAt;
    }

    /**
     * @return float
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @return int
     */
    public function getLoggedAt(): ?int
    {
        return $this->loggedAt;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }
}
