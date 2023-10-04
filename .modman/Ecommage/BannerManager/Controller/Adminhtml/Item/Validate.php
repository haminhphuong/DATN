<?php
declare(strict_types=1);
/**
 * Copyright © Thomas Nguyen, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\BannerManager\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\DataObject;

/**
 * Class for validation of banner item form on admin.
 */
class Validate extends Action implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecommage_BannerManager::banner';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \Ecommage\BannerManager\Model\Banner\ItemFactory
     */
    private $itemFactory;

    /**
     * Validate constructor.
     *
     * @param Action\Context                                   $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Ecommage\BannerManager\Model\Banner\ItemFactory      $itemFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Ecommage\BannerManager\Model\Banner\ItemFactory $itemFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->itemFactory = $itemFactory;
    }

    /**
     * AJAX customer address validation action
     *
     * @return Json
     */
    public function execute(): Json
    {
        $id = (int)$this->getRequest()->getParam('id');
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Framework\DataObject $response */
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);
        try {
            $resultJson = $this->resultJsonFactory->create();
            $model = $this->itemFactory->create()->load($id);
            $model->setData($data);
            $errors = $model->validate();
        } catch (\Magento\Framework\Validator\Exception $exception) {
            $response->setError(true);
            $response->setMessage($exception->getMessage());
        }

        if (is_array($errors) && !empty($errors)) {
            foreach ($errors as $error) {
                $response->setError(true);
                $response->setMessage($error);
            }
        }

        $resultJson->setData($response);
        return $resultJson;
    }
}
