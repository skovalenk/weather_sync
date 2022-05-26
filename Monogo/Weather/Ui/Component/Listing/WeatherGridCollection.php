<?php

namespace Monogo\Weather\Ui\Component\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Monogo\Weather\Model\ResourceModel\Operations\TotalNumberOfRecords;
use Monogo\Weather\Model\ResourceModel\WeatherRepository;

/**
 * DataProvider component.
 */
class WeatherGridCollection extends DataProvider
{
    /**
     * @var WeatherRepository
     */
    private WeatherRepository $weatherRepository;

    /**
     * @var TotalNumberOfRecords
     */
    private TotalNumberOfRecords $totalNumberOfRecords;

    /**
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param WeatherRepository $weatherRepository
     * @param TotalNumberOfRecords $totalNumberOfRecords
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        WeatherRepository $weatherRepository,
        TotalNumberOfRecords $totalNumberOfRecords,
        array $meta = [],
        array $data = [])
    {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->weatherRepository = $weatherRepository;
        $this->totalNumberOfRecords = $totalNumberOfRecords;
    }

    /**
     * We are not using collections in the sake of:
     *
     * - future upgradability, they are going to be deprecated as Active Record mechanism in general
     * - performance. Collection is always slower, than query, because of number of generic plugins/events triggered
     * for every collection
     * - hidden logic. There is a magic logic, that is happening on the background and is not visible.
     * I want to eliminate hidden logic in the code
     *
     * @return array
     */
    public function getData()
    {
        $page = $this->request->getParam('paging', ['current' => 1]);
        $weatherLogItems = [];

        foreach ($this->weatherRepository->getPage($page['current']) as $index => $weather) {
            $weatherLogItems[] = [
                //In order to be able to cache grid, it does not cache grid without entity_id
                'entity_id' => $page['current'] * WeatherRepository::DEFAULT_PAGE_SIZE + $index,
                'temperature' => $weather->getTemperature(),
                'country_id' => $weather->getCountryCode(),
                'city' => $weather->getCity(),
                'logged_at' => $weather->getLoggedAt(),
            ];
        }

        return [
            'items' => $weatherLogItems,
            'totalRecords' => $this->totalNumberOfRecords->execute([])
        ];
    }
}
