<?php

namespace Monogo\Weather\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Monogo\Weather\Spi\WeatherProviderInterface;

class WeatherProvidersPool implements OptionSourceInterface
{
    /**
     * @var WeatherProviderInterface[]
     */
    private array $providers;

    /**
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * Retrieve currently specified weather provider
     *
     * @param string $providerCode
     * @throws LocalizedException
     * @return WeatherProviderInterface
     */
    public function get(string $providerCode): WeatherProviderInterface
    {
        if (!isset($this->providers[$providerCode])) {
            throw new LocalizedException(__('Provider %1 should be declared', $providerCode));
        }

        return $this->providers[$providerCode];
    }

    /**
     * Retrieves an option sources for all available weather providers
     *
     * @return array|\string[][]
     */
    public function toOptionArray(): array
    {
        return array_map(function (string $providerKey) {
            return [
                'label' => $providerKey,
                'value' => $providerKey
            ];
        }, array_keys($this->providers));
    }
}
