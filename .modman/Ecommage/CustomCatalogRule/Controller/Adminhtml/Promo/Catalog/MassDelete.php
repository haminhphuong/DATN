<?php

namespace Ecommage\CustomCatalogRule\Controller\Adminhtml\Promo\Catalog;

use Magento\Backend\App\Action;
use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CatalogRuleRepositoryInterface
     */
    protected $catalogRuleRepository;

    public function __construct
    (
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CatalogRuleRepositoryInterface $catalogRuleRepository
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->catalogRuleRepository = $catalogRuleRepository;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
//        /** @var int[]|null $ids */
//        $ids = $this->getRequest()->getParam('ids');

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        if (is_array($collection->getItems())) {
            try {
                foreach ($collection->getItems() as $catalogRule) {
                    $this->catalogRuleRepository->deleteById((int)$catalogRule->getData('rule_id'));
                }

                $this->messageManager->addSuccessMessage(__('You deleted %1 rule(s).', count($collection->getItems())));

                return $this->_redirect('catalog_rule/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t delete the rule right now. Please review the log and try again.')
                );
            }

            return $this->_redirect('catalog_rule/*/');
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule(s) to delete.'));

        return $this->_redirect('catalog_rule/*/');
    }
}
