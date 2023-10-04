<?php

namespace Ecommage\CustomCatalog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Review\Model\Review;
use Ecommage\CustomCatalog\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class ReviewApproveBefore implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Review
     */
    protected $review;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Review $review
     * @param Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Review $review,
        Data $helper,
        LoggerInterface $logger
    ){
        $this->storeManager = $storeManager;
        $this->review = $review;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
       $storeId = $this->storeManager->getStore()->getId();
       $autoAppReview = $this->helper->getAutoApproveReviewConfig('general/enable',$storeId);
        $review = $observer->getDataByKey('object');
        $review->setTitle('');
        if($autoAppReview){
            try {
                  $review->setStatusId(Review::STATUS_APPROVED);
                  $review->setStoreId($storeId);
              }catch (\Exception $e){
                  $this->logger->error($e->getMessage());
              }
        }
    }
}
