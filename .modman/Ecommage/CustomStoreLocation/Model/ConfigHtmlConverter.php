<?php
/**
 * @author    Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package   Amasty_Storelocator
 */

namespace Ecommage\CustomStoreLocation\Model;

use Amasty\Storelocator\Model\BaseImageLocation;
use Amasty\Storelocator\Model\ConfigProvider;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Escaper;
use Amasty\Storelocator\Model\Location;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD)
 */
class ConfigHtmlConverter extends \Amasty\Storelocator\Model\ConfigHtmlConverter
{
    /**
     * @var \Amasty\Storelocator\Model\ConfigProvider
     */
    protected $configProvider;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Ecommage\Address\Model\CityFactory
     */
    protected $cityFactory;

    /**
     * @var Location
     */
    protected $location;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Amasty\Storelocator\Model\BaseImageLocation
     */
    protected $baseImageLocation;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ConfigProvider $configProvider
     * @param Escaper $escaper
     * @param FilterProvider $filterProvider
     * @param LoggerInterface $logger
     * @param CountryFactory $countryFactory
     * @param RegionFactory $regionFactory
     * @param UrlInterface $urlBuilder
     * @param BaseImageLocation $baseImageLocation
     * @param \Amasty\Storelocator\Helper\Data $dataHelper
     * @param \Ecommage\Address\Model\CityFactory $cityFactory
     * @param array $variableRenderers
     */
    public function __construct(
        ConfigProvider                      $configProvider,
        Escaper                             $escaper,
        FilterProvider                      $filterProvider,
        LoggerInterface                     $logger,
        CountryFactory                      $countryFactory,
        RegionFactory                       $regionFactory,
        UrlInterface                        $urlBuilder,
        BaseImageLocation                   $baseImageLocation,
        \Amasty\Storelocator\Helper\Data    $dataHelper,
        \Ecommage\Address\Model\CityFactory $cityFactory,
        array                               $variableRenderers = []
    )
    {
        $this->escaper = $escaper;
        $this->filterProvider = $filterProvider;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->baseImageLocation = $baseImageLocation;
        $this->logger = $logger;
        $this->cityFactory = $cityFactory;
        $this->configProvider = $configProvider;
        parent::__construct(
            $configProvider,
            $escaper,
            $filterProvider,
            $logger,
            $countryFactory,
            $regionFactory,
            $urlBuilder,
            $baseImageLocation,
            $dataHelper,
            $variableRenderers
        );
    }

    /**
     * @param $location
     */
    public function setHtml($location)
    {
        $this->location = $location;
        $this->location->setPhoto($this->baseImageLocation->getMainImageUrl($this->location));
        try {
            $this->location->setStoreListHtml($this->getStoreListHtml());
            $this->location->setPopupHtml($this->getPopupHtml());
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * Get store list html
     */
    protected function getStoreListHtml()
    {
        $storeListTemplate = $this->configProvider->getStoreListTemplate();

        return $this->replaceLocationValues($storeListTemplate);
    }

    /**
     * @param $location
     * @return string
     */
    public function getStoreListHtmlRewrite($location)
    {
        $storeListTemplate = $this->configProvider->getStoreListTemplate();

        return $this->replaceLocationValuesRewrite($storeListTemplate, $location);
    }

    /**
     * Get popup html
     */
    protected function getPopupHtml()
    {
        $baloon = $this->configProvider->getLocatorTemplate();

        return $this->replaceLocationValues($baloon);
    }

    /**
     * Return html with replaced values
     *
     * @param string $template
     *
     * @return string $html
     */
    protected function replaceLocationValues($template)
    {
        $locationData = $this->location->getData();
        $template = preg_replace_callback(
            '/{{if(?\'if\'.*)}}(.*|\n)*?{{\/\if(?P=if)}}/mixU',
            function ($match) use ($locationData) {
                if (!empty($locationData[$match['if']])) {
                    $value = $this->getPreparedValue($match['if']);

                    return str_replace(
                        [
                            '{{' . $match['if'] . '}}',
                            '{{if' . $match['if'] . '}}',
                            '{{/if' . $match['if'] . '}}'
                        ],
                        [$value, '', ''],
                        $match['0']
                    );
                }

                return '';
            },
            $template
        );

        $html = preg_replace_callback(
            '/{{(.*)}}/miU',
            function ($match) use ($locationData) {
                if (isset($locationData[$match['1']]) || isset($locationData['attributes'][$match['1']])) {
                    if (isset($locationData['attributes'][$match['1']])) {
                        return $this->convertAttributeData($locationData['attributes'][$match['1']]);
                    }

                    return $this->getPreparedValue($match['1']);
                } else {
                    return '';
                }
            },
            $template
        );

        return $html;
    }

    /**
     * @param $template
     * @param $location
     * @return string|string[]|null
     */
    public function replaceLocationValuesRewrite($template, $location)
    {
        $locationData = $location->getData();
        $template = preg_replace_callback(
            '/{{if(?\'if\'.*)}}(.*|\n)*?{{\/\if(?P=if)}}/mixU',
            function ($match) use ($location, $locationData) {
                if (!empty($locationData[$match['if']])) {
                    $value = $this->getPreparedValueRewrite($match['if'], $location);

                    return str_replace(
                        [
                            '{{' . $match['if'] . '}}',
                            '{{if' . $match['if'] . '}}',
                            '{{/if' . $match['if'] . '}}'
                        ],
                        [$value, '', ''],
                        $match['0']
                    );
                }

                return '';
            },
            $template
        );

        $html = preg_replace_callback(
            '/{{(.*)}}/miU',
            function ($match) use ($location, $locationData) {
                if (isset($locationData[$match['1']]) || isset($locationData['attributes'][$match['1']])) {
                    if (isset($locationData['attributes'][$match['1']])) {
                        return $this->convertAttributeData($locationData['attributes'][$match['1']]);
                    }

                    return $this->getPreparedValueRewrite($match['1'], $location);
                } else {
                    return '';
                }
            },
            $template
        );

        return $html;
    }

    /**
     * Get prepared value by key
     *
     * @param string $key
     *
     * @return string
     */
    protected function getPreparedValue($key)
    {
        switch ($key) {
            case 'name':
                if ($this->location->getUrlKey() && $this->configProvider->getEnablePages()) {
                    return '<div class="amlocator-title"><a class="amlocator-link" href="' . $this->getLocationUrl()
                        . '" title="' . $this->escaper->escapeHtml($this->location->getData($key))
                        . '" target="_blank">'
                        . $this->escaper->escapeHtml($this->location->getData($key))
                        . '</a></div>';
                }

                return '<div class="amlocator-title">' . $this->escaper->escapeHtml($this->location->getData($key))
                    . '</div>';
            case 'description':
            case 'short_description':
                return $this->getPreparedDescription($key);
            case 'country':
                return $this->escaper->escapeHtml($this->getCountryName());
            case 'state':
                return $this->escaper->escapeHtml($this->getStateName());
            case 'city':
                return $this->escaper->escapeHtml($this->getCityName());
            case 'rating':
                return $this->location->getData($key);
            case 'photo':
                $photo = $this->location->getData($key);

                return '<div class="amlocator-image"><img src="' . $this->escaper->escapeUrl($photo) . '" alt="amlocator image"></div>';
            default:
                return $this->escaper->escapeHtml($this->location->getData($key));
        }
    }

    /**
     * @param $key
     * @param $location
     * @return array|string
     */
    public function getPreparedValueRewrite($key, $location)
    {
        switch ($key) {
            case 'name':
                if ($location->getUrlKey() && $this->configProvider->getEnablePages()) {
                    return '<div class="amlocator-title"><a class="amlocator-link" href="' . $this->getLocationUrlRewrite($location)
                        . '" title="' . $this->escaper->escapeHtml($location->getData($key))
                        . '" target="_blank">'
                        . $this->escaper->escapeHtml($location->getData($key))
                        . '</a></div>';
                }
                $amlocatorTitleHtmls = '<div class="amlocator-title">' . $this->escaper->escapeHtml($location->getData($key));

                return $amlocatorTitleHtmls .'</div>';
            default:
                return $this->escaper->escapeHtml($location->getData($key));
        }
    }

    /**
     * Get prepared description
     *
     * @return string
     */
    public function getPreparedDescription($key)
    {
        $descriptionLimit = $this->configProvider->getDescriptionLimit();
        $description = $this->filterProvider->getPageFilter()->filter($this->location->getData($key));
        $description = strip_tags(
            preg_replace('#(<style.*?>).*?(</style>)#', '$1$2', $description)
        );
        if (strlen($description) < $descriptionLimit) {
            return '<div class="amlocator-description">' . $description . '</div>';
        }

        if ($descriptionLimit) {
            if (preg_match('/^(.{' . ($descriptionLimit) . '}.*?)\b/isu', $description, $matches)) {
                $description = $matches[1] . '...';
            }

            if ($this->configProvider->getEnablePages()) {
                $description .= '<a href="' . $this->getLocationUrl() . '" title="read more" target="_blank"> '
                    . __('Read More') . '</a>';
            }
        }

        return '<div class="amlocator-description">' . $description . '</div>';
    }

    /**
     * Convert attributes data to html
     *
     * @param array $attributeData
     *
     * @return string $html
     */
    protected function convertAttributeData($attributeData)
    {
        $html = $this->escaper->escapeHtml($attributeData['frontend_label']) . ':<br>';
        if (isset($attributeData['option_title']) && is_array($attributeData['option_title'])) {
            foreach ($attributeData['option_title'] as $option) {
                $html .= '- ' . $this->escaper->escapeHtml($option) . '<br>';
            }
            return $html;
        } else {
            $value = isset($attributeData['option_title']) ? $attributeData['option_title'] : $attributeData['value'];

            return $html . $this->escaper->escapeHtml($value) . '<br>';
        }
    }

    /**
     * Get country name
     *
     * @return string
     */
    protected function getCountryName()
    {
        return $this->countryFactory->create()->loadByCode($this->location->getCountry())->getName();
    }

    /**
     * Get state name
     *
     * @return string
     */
    protected function getStateName()
    {
        if ($this->location) {
            $stateName = $this->regionFactory->create()->load($this->location->getState())->getName();
            return $stateName ? $stateName : $this->location->getState();
        }
        return null;
    }

    /**
     * @return string
     */
    protected function getCityName()
    {
        if ($this->location) {
            $stateName = $this->cityFactory->create()->load($this->location->getCityId())->getName();
            return $stateName ? $stateName : $this->location->getCity();
        }
        return null;
    }

    /**
     * Get location url
     *
     * @return string
     */
    protected function getLocationUrl()
    {
        return $this->escaper->escapeUrl(
            $this->urlBuilder->getUrl($this->configProvider->getUrl() . '/' . $this->location->getUrlKey())
        );
    }

    /**
     * @param $location
     * @return array|string
     */
    public function getLocationUrlRewrite($location)
    {
        return $this->escaper->escapeUrl(
            $this->urlBuilder->getUrl($this->configProvider->getUrl() . '/' . $location->getUrlKey())
        );
    }
}
