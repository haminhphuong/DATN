<?php
namespace Ecommage\Backend\Cron;

use Ecommage\Backend\Model\ResourceModel\ScheduleProduct\CollectionFactory as ScheduleCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Model\ProductRepository;

class CronUpdateInfoProduct
{
    /**
     * @var ScheduleCollection
     */
    protected $scheduleCollection;
    /**
     * @var DateTime
     */
    protected $dateTime;
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @param ScheduleCollection $scheduleCollection
     * @param DateTime $dateTime
     * @param ProductRepository $productRepository
     */
    public function __construct
    (
        ScheduleCollection $scheduleCollection,
        DateTime $dateTime,
        ProductRepository $productRepository
    )
    {
        $this->scheduleCollection = $scheduleCollection;
        $this->dateTime = $dateTime;
        $this->productRepository = $productRepository;
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute(){

        $currentDate = $this->dateTime->gmtDate();
        $updateCollection = $this->scheduleCollection->create()->addFieldToFilter('schedule_date_start',['lteq' => $currentDate])
            ->addFieldToFilter('schedule_date_end',['gteq' => $currentDate])
            ->addFieldToFilter('status',['eq'=>1]);
        foreach ($updateCollection as $update) {
            $info = json_decode($update->getInfo());
            $product = $this->productRepository->getById($update->getProductId());
            foreach ($info as $key=>$value){

                switch ($key) {
                    case 'quantity_and_stock_status':
                        $product->setStockData(['qty'=>isset($value->qty) ? $value->qty : 0, 'is_in_stock'=>$value->is_in_stock]);
                        $product->setQuantityAndStockStatus(['qty'=>isset($value->qty) ? $value->qty : 0,'is_in_stock'=>$value->is_in_stock]);
                        $this->productRepository->save($product);
                        break;
                    case 'category_ids':
                        $product->setCategoryIds($value);
                        $this->productRepository->save($product);
                        break;
                    default:
                        if(!$value){
                            $value = ' ';
                        }
                        $product->addAttributeUpdate($key,$value,$update->getStoreId());
                        break;
                }
            }
            $update->setStatus(0)->save();
        }
    }
}
