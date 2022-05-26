<?php

namespace Monogo\Weather\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Monogo\Weather\Model\ResourceModel\WeatherRepository;
use Monogo\Weather\Model\Source\AvailableCities;
use Monogo\Weather\Model\WeatherProvidersPool;
use Monogo\Weather\Model\WeatherFactory;

class Synchronise
{
    private const XML_PATH_AVAILABLE_CITIES = 'weather/general/available_cities';
    private const XML_PATH_PROVIDER = 'weather/general/provider';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var WeatherProvidersPool
     */
    private WeatherProvidersPool $weatherProvidersPool;

    /**
     * @var WeatherRepository
     */
    private WeatherRepository $weatherRepository;

    /**
     * @var WeatherFactory
     */
    private WeatherFactory $weatherFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param WeatherProvidersPool $weatherProvidersPool
     * @param WeatherRepository $weatherRepository
     * @param WeatherFactory $weatherFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WeatherProvidersPool $weatherProvidersPool,
        WeatherRepository $weatherRepository,
        WeatherFactory $weatherFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->weatherProvidersPool = $weatherProvidersPool;
        $this->weatherRepository = $weatherRepository;
        $this->weatherFactory = $weatherFactory;
    }

    /**
     * CRON to update temperature
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(): void
    {
        $availableCities = $this->scopeConfig->getValue(self::XML_PATH_AVAILABLE_CITIES);
        $provider = $this->scopeConfig->getValue(self::XML_PATH_PROVIDER);
        //Split by comma, as there can be multiple values
        $availableCities = explode(",", $availableCities);

        foreach ($availableCities as $availableCity) {
            list($countryCode, $city) = explode(AvailableCities::CITY_COUNTRY_DELIMITER, $availableCity);
            //Getting temperature from API
            $temperature = $this->weatherProvidersPool->get($provider)->get($countryCode, $city);
            //Creating DTO object
            $weather = $this->weatherFactory->create([
                'city' => $city,
                'countryCode' => $countryCode,
                'temperature' => $temperature
            ]);
            //Persisting data in a database
            $this->weatherRepository->save($weather);
        }
    }
}
