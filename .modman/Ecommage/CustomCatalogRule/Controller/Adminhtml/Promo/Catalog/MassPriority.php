<?php

namespace Ecommage\CustomCatalogRule\Controller\Adminhtml\Promo\Catalog;

use Magento\Backend\App\Action;
use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;

class MassPriority extends Action
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

    /**
     * @param Action\Context $context
     * @param CatalogRuleRepositoryInterface $catalogRuleRepository
     */
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
                    /** @var \Magento\SalesRule\Model\Rule $rule */
                    $rule = $this->catalogRuleRepository->get((int)$catalogRule->getData('rule_id'));
                    $rulePriority = $this->getRulePriority($rule);
                    $rule->setSortOrder($rulePriority);
                    $this->catalogRuleRepository->save($rule);
                }

                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been updated.', count($collection->getItems()))
                );

                return $this->_redirect('catalog_rule/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while updating the rule(s) priority.')
                );
            }

            return $this->_redirect('catalog_rule/*/');
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a rule to update its priority.'));

        return $this->_redirect('catalog_rule/*/');
    }

    /**
     * @param $rule
     * @return int
     */
    public function getRulePriority($rule)
    {
        /** @var string|null $priority */
        $priority = $this->getRequest()->getParam('priority');
        $rulePriority = $rule->getSortOrder();

        if ($priority === 'low') {
            $rulePriority++;
        } else {
            if ($rulePriority != 0) {
                $rulePriority--;
            }
        }

        return $rulePriority;
    }
}
