<?php

namespace Ecommage\CustomTheme\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
/**
 * Class Data
 *
 * @package Ecommage\CustomTheme\Helper
 */
class Data extends AbstractHelper
{
    const PATH_SCRIPT_AFTER_BODY_CODE = 'script_after_body/general/code';
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct
    (
        Context $context,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function getImageFullPath()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $fileId = 'svg/svg-sprites.svg';
        $params = [
                    'area' => 'frontend' //for admin area its backend
                ];
        $fileSystem = $objectManager->create('\Magento\Framework\View\Asset\Repository');
        $asset = $fileSystem->createAsset($fileId, $params);
        return $asset->getSourceFile();
    }

    /**
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageFullPathDhts()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $fileId = 'svg/svg-sprites_dhts.svg';
        $params = [
                    'area' => 'frontend' //for admin area its backend
                ];
        $fileSystem = $objectManager->create('\Magento\Framework\View\Asset\Repository');
        $asset = $fileSystem->createAsset($fileId, $params);
        return $asset->getSourceFile();
    }

    /**
     * @return mixed
     */
    public function getScriptAfterBody(){
        $storeId = $this->storeManager->getStore()->getStoreId();
        return $this->scopeConfig->getValue(self::PATH_SCRIPT_AFTER_BODY_CODE, ScopeInterface::SCOPE_STORE, $storeId);
    }

}
