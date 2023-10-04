<?php
namespace Ecommage\CustomCatalog\Observer\CatalogProduct;

use Magento\Catalog\Model\ProductRepository;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;

class UpdateAttributeFullImages implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ProductRepository
     */
    protected $productRepository;
    /**
     * @var Attribute
     */
    protected $eavAttribute;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct
    (
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository,
        Attribute $eavAttribute,
        ResourceConnection $resourceConnection
    )
    {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->eavAttribute = $eavAttribute;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $count = 0;

        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $product = $observer->getData('product');
        $storeIds = $product->getStoreIds();
        array_push($storeIds,0);
        $images = $product->getMediaGalleryImages();

        $muiltiImage = '';
        foreach($images as $child){
            $count ++;
            $muiltiImage .= $child->getUrl();
            if($count < count($images)){
                $muiltiImage .= ',';
            }
        }

        foreach ($storeIds as $id){
            if($id == 17){
                continue;
            }
            $productRepos = $this->productRepository->get($product->getSku(), false, $id);
            if ($id == 0){
                $productRepos = $this->productRepository->get($product->getSku(), false, 1);
            }
            $baseUrlByStore = $this->storeManager->getStore($id)->getBaseUrl();
            $muiltiImage = str_replace($baseUrl,$baseUrlByStore,$muiltiImage);
            $productUrl = $productRepos->getUrlInStore();
            try {
                $productRepos->addAttributeUpdate('full_product', $productUrl, $id);
                $productRepos->addAttributeUpdate('full_images', $muiltiImage, $id);
            }catch (\Exception $e){
                $this->logger->error($e->getMessage());
            }
        }
    }

}
