<?php

namespace Ecommage\BannerManager\Controller\Adminhtml\Item;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Ecommage\BannerManager\Api\Data\ItemInterfaceFactory;
use Ecommage\BannerManager\Model\Banner\ItemFactory;
use Ecommage\BannerManager\Model\BannerRepository;
use Ecommage\BannerManager\Model\BannerItemRepository;
use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Ecommage\BannerManager\Model\System\Config\Status;
use Ecommage\BannerManager\Model\Banner\FileInfo;
use Ecommage\BannerManager\Model\Banner\Item;
use Magento\Ui\Component\Form\Element\Wysiwyg;

/**
 * Class Save
 */
class Save extends BannerItem
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var BannerItemRepository
     */
    protected $bannerItemRepository;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ItemInterfaceFactory
     */
    private $bannerItemDataFactory;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var BannerRepository
     */
    private $bannerRepository;
    /**
     * @var
     */
    private $imageUploader;

    /**
     * Save constructor.
     *
     * @param Context                $context
     * @param LoggerInterface        $logger
     * @param ItemFactory            $itemFactory
     * @param PageFactory            $resultPageFactory
     * @param BannerRepository       $bannerRepository
     * @param DataObjectHelper       $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param BannerItemRepository   $bannerItemRepository
     * @param ItemInterfaceFactory   $bannerItemDataFactory
     * @param JsonFactory            $resultJsonFactory
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        ItemFactory $itemFactory,
        PageFactory $resultPageFactory,
        BannerRepository $bannerRepository,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        BannerItemRepository $bannerItemRepository,
        ItemInterfaceFactory $bannerItemDataFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->logger                = $logger;
        $this->dataPersistor         = $dataPersistor;
        $this->dataObjectHelper      = $dataObjectHelper;
        $this->bannerRepository      = $bannerRepository;
        $this->resultJsonFactory     = $resultJsonFactory;
        $this->bannerItemRepository  = $bannerItemRepository;
        $this->bannerItemDataFactory = $bannerItemDataFactory;
        parent::__construct($context, $itemFactory, $dataPersistor, $resultPageFactory);
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @return Json
     */
    public function execute(): Json
    {
        $error        = false;
        $data         = $this->getRequest()->getPostValue();
        if(!isset($data[Item::IMAGE_DESKTOP])){
            $data[Item::IMAGE_DESKTOP] = '';
        }
        if(!isset($data[Item::IMAGE_MOBILE])){
            $data[Item::IMAGE_MOBILE] = '';
        }
        $bannerId     = (int)$this->getRequest()->getParam(Item::BANNER_ID,0);
        $bannerItemId = (int)$this->getRequest()->getParam(Item::BANNER_ITEM_ID,0);
        $message      = __('We can\'t change banner item right now.');
        if ($data && $bannerId) {
            $data[Item::BANNER_ID] = $bannerId;
            if (isset($data[Item::IS_ACTIVE]) && $data[Item::IS_ACTIVE] === 'true') {
                $data[Item::IS_ACTIVE] = Status::STATUS_ENABLED;
            }

            if (empty($data[Item::BANNER_ITEM_ID])) {
                $data[Item::BANNER_ITEM_ID] = null;
            }

            if (!empty($data[Item::IMAGE_DESKTOP])) {
                $this->moveFileFromTmp($data);
            }

            /** @var \Ecommage\BannerManager\Model\Banner\Item $model */
            $model = $this->_itemFactory->create();
            if ($bannerItemId) {
                $model = $this->bannerItemRepository->getById($bannerItemId);
            }

            $model->setData($data);
            try {
                $savedBannerItem = $this->bannerItemRepository->save($model);
                if ($bannerItemId) {
                    $message = __('Banner Item has been updated.');
                } else {
                    $bannerItemId = $savedBannerItem->getId();
                    $message      = __('New banner item has been added.');
                }
            } catch (LocalizedException $e) {
                $error   = true;
                $message = __($e->getMessage());
            } catch (\Exception $e) {
                $error   = true;
                $message = __('We can\'t change banner item right now.');
            }
        }
        $bannerItemId = empty($bannerItemId) ? null : $bannerItemId;
        $resultJson   = $this->resultJsonFactory->create();
        $resultJson->setData(
            [
                'message' => $message,
                'error'   => $error,
                'data'    => [
                    Item::BANNER_ITEM_ID => $bannerItemId
                ]
            ]
        );

        return $resultJson;
    }

    /**
     * @param $image
     *
     * @return mixed
     */
    protected function moveFileFromTmp(&$data)
    {
        foreach ([Item::IMAGE_DESKTOP, Item::IMAGE_MOBILE] as $imageAttr) {
            if (empty($data[$imageAttr])) {
                continue;
            }

            $image = $data[$imageAttr];
            $value = substr($image[0]['url'],stripos($image[0]['url'],'___directive'));
            if(strpos($value,'media') === false){
                $param = explode('/',$value);
                $url = base64_decode(rtrim($param[1],','));
                $link = explode('"',$url);
                $image[0]['url'] = Wysiwyg::NAME . $link[1];
            }

            if (isset($image[0]['name'])) {
                $urlImage = $image[0]['url'];
                if (!empty($image[0]['tmp_name'])) {
                    /** @var \Ecommage\BannerManager\Model\ImageUploader imageUploader */
                    $this->imageUploader = $this->_objectManager->get(
                        'Ecommage\BannerManager\BannerImageUpload'
                    );
                    $this->imageUploader->moveFileFromTmp($image);
                    $urlImage = str_replace('banner/tmp/item','banner/item', $urlImage);
                }

                $image = substr($urlImage, strpos($urlImage, '/media/') + 7);
            }
            $data[$imageAttr] = $image;
        }

        return $data;
    }
}
