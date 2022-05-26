<?php

namespace Monogo\Weather\Section;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Monogo\Weather\Api\Data\WeatherInterface;
use Monogo\Weather\Model\ResourceModel\WeatherRepository;
use Monogo\Weather\Spi\CountryAndCityResolverInterface;

class WeatherSection implements SectionSourceInterface
{
    private const XML_PATH_SECTION_DATA_LIFETIME = 'customer/online_customers/section_data_lifetime';
    //Weather lifetime. It is time in seconds, when weather is actual. After 10 minutes it is becoming not
    //actual
    public const WEATHER_LIFETIME = 10 * 60;

    /**
     * @var WeatherRepository
     */
    private WeatherRepository $weatherRepository;

    /**
     * @var CountryAndCityResolverInterface
     */
    private CountryAndCityResolverInterface $countryAndCityResolver;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param WeatherRepository $weatherRepository
     * @param CountryAndCityResolverInterface $countryAndCityResolver
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        WeatherRepository $weatherRepository,
        CountryAndCityResolverInterface $countryAndCityResolver,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->weatherRepository = $weatherRepository;
        $this->countryAndCityResolver = $countryAndCityResolver;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieves the temperature for particular user with a help of Magento private content
     *
     * @return array
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function getSectionData(): array
    {
        $weather = $this->weatherRepository->getByCountryAndCity(
            $this->countryAndCityResolver->getCountry(),
            $this->countryAndCityResolver->getCity()
        );

        return [
            'temperature' => $weather->getTemperature(),
            'data_id' => $this->getDataId($weather)
        ];
    }

    /**
     * data_id - is parameter, that is assigned to the every section in a private content
     * According to this parameter, Magento on the frontend is validating for how long the section is
     * valid.
     *
     * Here is how validation is working:
     *
     * data_id + sectionDataLifeTime <= currentTimeStamp
     *
     * In our case we need to say that data_id should be valid for 10 minutes since it was updated.
     * It is not 10 ultimatively 10 minutes. It can be 1 minute, if customer requested weather, after
     * 9 minutes since latest update.
     *
     * Here is the formula:
     *
     * data_id = weatherSyncTime - sectionDataLifeTime (we need to eliminate original life time) + lifetimeOfWeather
     *
     * @param WeatherInterface $weather
     * @return int
     */
    private function getDataId(WeatherInterface $weather): int
    {
        //Data id is unix timestamp by default
        $loggedAt = $weather->getLoggedAt();
        //Calculating seconds of section data lifetime
        $sectionDataLifeTime = $this->scopeConfig->getValue(self::XML_PATH_SECTION_DATA_LIFETIME) * 60;
        return $loggedAt - $sectionDataLifeTime + self::WEATHER_LIFETIME;
    }
}
