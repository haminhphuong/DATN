<?php

namespace Ecommage\CustomCatalog\Controller\Adminhtml\Promo\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\SalesRule\Api\RuleRepositoryInterface;

class DuplicateCartRule extends Action
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    public function __construct(
        Action\Context $context,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $ruleId = (int)$this->getRequest()->getParam('id');

        if ($ruleId) {
            try {
                $rule = $this->ruleRepository->getById($ruleId);
                $rule->setRuleId(null);
                $newRule = $this->ruleRepository->save($rule);
                $this->messageManager->addSuccessMessage(__('The rule has been duplicated.'));

                return $resultRedirect->setPath('sales_rule/*/edit', ['id' => $newRule->getRuleId()]);
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t duplicate the rule right now. Please review the log and try again.')
                );
            }

            return $resultRedirect->setPath('sales_rule/*/');
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to duplicate.'));

        return $resultRedirect->setPath('sales_rule/*/');
    }
}
