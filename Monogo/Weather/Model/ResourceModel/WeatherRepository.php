<?php

namespace Monogo\Weather\Model\ResourceModel;

use Magento\Framework\Exception\NotFoundException;
use Monogo\Weather\Api\Data\WeatherInterface;
use Monogo\Weather\Model\WeatherFactory;
use Monogo\Weather\Model\ResourceModel\Operations\Create;
use Monogo\Weather\Model\ResourceModel\Operations\Read;

class WeatherRepository
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @var Create
     */
    private Create $createOperation;

    /**
     * @var Read
     */
    private Read $readOperation;

    /**
     * @var WeatherFactory
     */
    private WeatherFactory $weatherFactory;

    /**
     * @param Create $createOperation
     * @param Read $readOperation
     * @param WeatherFactory $weatherFactory
     */
    public function __construct(
        Create $createOperation,
        Read $readOperation,
        WeatherFactory $weatherFactory
    ) {
        $this->createOperation = $createOperation;
        $this->readOperation = $readOperation;
        $this->weatherFactory = $weatherFactory;
    }

    /**
     * We should not use localized time, as the same temperature can be used for different store_views
     * and for different customers with different timestamps
     *
     * @return int
     */
    private function getCurrentTimestamp(): int
    {
        return time();
    }

    /**
     * Pagination for weather log records. By default 20 records as a page size is used.
     * You can pass a page, to get a concrete page object
     *
     * @param int $page
     * @return WeatherInterface[]
     */
    public function getPage(int $page): array
    {
        $weatherLog = [];
        //We are extracting all the data limited by pages
        $rawWeatherLog = $this->readOperation->execute([
            Read::PAGE_OFFSET => $page,
            Read::LIMIT => self::DEFAULT_PAGE_SIZE
        ]);

        foreach ($rawWeatherLog as $rawWeather) {
            //Some mapping between database and DTO
            $rawWeather['temperature'] = $rawWeather['value'];
            $rawWeather['countryCode'] = $rawWeather['country_id'];
            $weatherLog[] = $this->weatherFactory->create($rawWeather);
        }

        return $weatherLog;
    }

    /**
     * Searching the current weather by country and city
     * If city is not available is making fallback and is searching only by country
     * If country is not available throwing NotFoundException
     *
     * @param string $country
     * @param string $city
     * @return WeatherInterface
     */
    public function getByCountryAndCity(string $country, string $city): WeatherInterface
    {
        $weather = $this->readOperation->execute(['country_id' => $country, 'city' => $city]);
        //Let`s say that customer is living in a city, weather for which is not available
        //Let`s use fallback mechanism and retrieve the weather in country for him
        if (empty($weather)) {
            $weather = $this->readOperation->execute(['country_id' => $country]);
        }
        $weather = reset($weather);
        if (empty($weather)) {
            throw new NotFoundException(__('Country and City weather is not available yet'));
        }

        return $this->weatherFactory->create([
            'city' => $city,
            'countryCode' => $country,
            'temperature' => $weather['value'],
            'loggedAt' => $weather['logged_at']
        ]);
    }

    /**
     * This is a simple operation of adding new temperature by city and country into the table
     *
     * @param WeatherInterface $weather
     */
    public function save(WeatherInterface $weather): void
    {
        $this->createOperation->execute([
            'city' => $weather->getCity(),
            'country_id' => $weather->getCountryCode(),
            'value' => $weather->getTemperature(),
            'logged_at' => $this->getCurrentTimestamp()
        ]);
    }
}
