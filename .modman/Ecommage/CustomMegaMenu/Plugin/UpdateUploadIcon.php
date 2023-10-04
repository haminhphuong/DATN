<?php
namespace Ecommage\CustomMegaMenu\Plugin;

use Magento\Backend\App\Action\Context;
use Magento\Cms\Controller\Adminhtml\Wysiwyg\Images;
use Magento\Cms\Model\Wysiwyg\Images\GetInsertImageContent;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;

/**
 *
 */
class UpdateUploadIcon
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var GetInsertImageContent
     */
    private $getInsertImageContent;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param RawFactory $resultRawFactory
     * @param GetInsertImageContent $getInsertImageContent
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        RawFactory $resultRawFactory,
        ?GetInsertImageContent $getInsertImageContent = null
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->getInsertImageContent = $getInsertImageContent ?: ObjectManager::getInstance()->get(GetInsertImageContent::class);
    }

    /**
     * Return a content (just a link or an html block) for inserting image to the content
     *
     * @return ResultInterface
     */
    public function aroundExecute(\Magento\Cms\Controller\Adminhtml\Wysiwyg\Images\OnInsert $subject, callable $callable)
    {
        $data = $subject->getRequest()->getParams();
        $data['force_static_path'] = false;
        return $this->resultRawFactory->create()->setContents(
            $this->getInsertImageContent->execute(
                $data['filename'],
                $data['force_static_path'],
                $data['as_is'],
                isset($data['store']) ? (int) $data['store'] : null
            )
        );
    }
}
