<?php
namespace Ecommage\TabsPro\Preference\Helper;

use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DataObjectFactory;

class ImageBuilder extends \Magezon\TabsPro\Helper\ImageBuilder
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;
    /**
     * @var ManagerInterface
     */
    private $eventManager;
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param HelperFactory $helperFactory
     * @param ImageFactory $imageFactory
     * @param ProductMetadataInterface $productMetadata
     * @param ManagerInterface $eventManager
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct
    (
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        ProductMetadataInterface $productMetadata,
        ManagerInterface $eventManager,
        DataObjectFactory $dataObjectFactory
    )
    {
        $this->eventManager = $eventManager;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productMetadata = $productMetadata;
        parent::__construct($helperFactory, $imageFactory);
    }

    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create(Product $product = null, $imageId = null, array $attributes = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attrs = [];
        if ($this->getImageWidth()) {
            $attrs['width'] = $this->getImageWidth();
        }

        if ($this->getImageHeight()) {
            $attrs['height'] = $this->getImageHeight();
        }

        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()->init($this->product, $this->imageId, $attrs);

        $template = $helper->getFrame()
            ? 'Magento_Catalog::product/image.phtml'
            : 'Magento_Catalog::product/image_with_borders.phtml';

        $imagesize   = $helper->getResizedImageInfo();
        $imageWidth  = $this->getImageWidth()?$this->getImageWidth():$helper->getWidth();
        $imageHeight = $this->getImageHeight()?$this->getImageHeight():$helper->getHeight();

        if ($this->getResizeImageWidth()) {
            $resizeImageWidth = $this->getResizeImageWidth();
        } else {
            $resizeImageWidth  = !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth();
        }
        if ($this->getResizeImageHeight()) {
            $resizeImageHeight = $this->getResizeImageHeight();
        } else {
            $resizeImageHeight = !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight();
        }

        $data = [
            'template'             => $template,
            'image_url'            => $helper->getUrl(),
            'width'                => $imageWidth,
            'height'               => $imageHeight,
            'label'                => $helper->getLabel(),
            'ratio'                => $this->getRatio($helper),
            'custom_attributes'    => $this->getCustomAttributes(),
            'resized_image_width'  => $resizeImageWidth,
            'resized_image_height' => $resizeImageHeight,
            'class'                => 'product-image-photo'
        ];

        if ($this->getLazyLoad())  {
            if (is_array($this->getCustomAttributes())) {
                $data['custom_attributes']['data-src'] = $helper->getUrl();
            } else {
                $data['custom_attributes'] .= ' data-src="' . $helper->getUrl() . '"';
            }
            $data['image_url']         = '';
        }
        $data['custom_attributes']['width'] = '100%';
        $data['custom_attributes']['height'] = '100%';
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');

        if ($productMetadata->getVersion() < '2.3.0') {
            return $this->imageFactory->create(['data' => $data]);
        } else {
            $helper = $this->imageFactory->create($this->product, $this->imageId, $attrs);
            $helper->setData($data);
            return $helper;
        }
    }
}
