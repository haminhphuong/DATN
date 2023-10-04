<?php
/**
 * download
 *
 * @copyright Copyright © 2022 Ecommage. All rights reserved.
 * @author    phuonghm@ecommage.com
 */

namespace Ecommage\Backend\Controller\Adminhtml\Product;


use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class Download extends \Magento\Backend\App\Action
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     */
    public function __construct
    (
        Context $context,
        FileFactory $fileFactory
    )
    {
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $scheduleId = $this->getRequest()->getParam('schedule_id');
        $filepath = 'schedule/schedule_product_'.$scheduleId.'_'.$productId . '.csv';
        $content['type'] = 'filename';
        $content['value'] = $filepath;
        $content['rm'] = '0';

        $csvfilename = 'schedule_product_'.$scheduleId.'_'.$productId.'.csv';
        return $this->fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }
}
