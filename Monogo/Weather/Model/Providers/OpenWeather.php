<?php

namespace Monogo\Weather\Model\Providers;

use GuzzleHttp\Client;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Monogo\Weather\Spi\WeatherProviderInterface;

class OpenWeather implements WeatherProviderInterface
{
    private const API_ENDPOINT = 'https://api.openweathermap.org/data/2.5/weather';
    private const XML_PATH_API_KEY = 'weather/general/open_weather_api_key';
    private const DEFAULT_UNITES = 'metric';

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param Client $client
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Client $client, ScopeConfigInterface $scopeConfig)
    {
        $this->client = $client;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * We are retrieving api_key from the admin panel.
     * If api key is not set, we are throwing an exception.
     *
     * @return string
     * @throws LocalizedException
     */
    private function getApiKey(): string
    {
        $apiKey = $this->scopeConfig->getValue(self::XML_PATH_API_KEY);

        if (!$apiKey) {
            throw new LocalizedException(__('Weather API key should be specified'));
        }

        return $apiKey;
    }

    /**
     * We are making request by
     *
     * @param string $countryCode
     * @param string $city
     * @return float
     * @throws LocalizedException
     */
    public function get(string $countryCode, string $city): float
    {
        $result = $this->client->get(
            self::API_ENDPOINT,
            [
                'query' => [
                    'appid' => $this->getApiKey(),
                    'units' => self::DEFAULT_UNITES,
                    'q' => sprintf('%s,%s', $city, $countryCode)
                ]
            ]
        );

        $result = json_decode($result->getBody()->getContents(), true);
        //In case if response is not readable we need to throw an exception
        if (!isset($result['main']['temp'])) {
            throw new LocalizedException(__('Weather request wasn`t successfull'));
        }
        //temperature always should be converted to decimal/float value
        return (float) $result['main']['temp'];
    }
}
