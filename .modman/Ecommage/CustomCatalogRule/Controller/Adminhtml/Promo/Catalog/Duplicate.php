<?php

namespace Ecommage\CustomCatalogRule\Controller\Adminhtml\Promo\Catalog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\CatalogRule\Api\Data\RuleInterface;
use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 *
 */
class Duplicate extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var CatalogRuleRepositoryInterface
     */
    protected $resultRepository;

    /**
     * @param CatalogRuleRepositoryInterface            $ruleRepository
     * @param Context                                   $context
     * @param Date                                      $dateFilter
     * @param DataPersistorInterface                    $dataPersistor
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param TimezoneInterface                         $localeDate
     */
    public function __construct(
        \Magento\CatalogRule\Api\CatalogRuleRepositoryInterface $ruleRepository,
        Context $context,
        Date $dateFilter,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        TimezoneInterface $localeDate
    ) {
        $this->resultRepository = $ruleRepository;
        $this->resource = $resourceConnection;
        $this->dataPersistor = $dataPersistor;
        $this->localeDate = $localeDate;
        parent::__construct($context);
    }

    /**
     * Execute save action from catalog rule
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $ruleId = $this->getRequest()->getParam('rule_id');
        $isStatus = 0;
        if ($ruleId) {
            /** @var \Magento\CatalogRule\Model\Rule $model */
            $model = $this->_objectManager->create(\Magento\CatalogRule\Model\Rule::class);
            try {
                $data = $this->resultRepository->get($ruleId);
                if ($data->getIsActive()){
                    $isStatus = 1;
                }
                $data->setRuleId(null);
                $data->setIsActive(0);
                if (!$data['from_date']) {
                    $data['from_date'] = $this->localeDate->formatDate();
                }

                if (isset($data['rule'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                    unset($data['rule']);
                }

                $model->loadPost($data->getData());

                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data->getData());
                $this->dataPersistor->set('catalog_rule', $data->getData());

                $newRule = $this->resultRepository->save($model);
                if ($isStatus == 1){
                    $this->setIsActiveRules($newRule->getRuleId(),$isStatus);
                }

                $this->messageManager->addSuccessMessage(__('The rule has been duplicated.'));

                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                $this->dataPersistor->clear('catalog_rule');
                return $this->_redirect('catalog_rule/*/edit', ['id' => $newRule->getRuleId()]);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t duplicate the rule right now. Please review the log and try again.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data->getData());
                $this->dataPersistor->set('catalog_rule', $data->getData());
                $this->_redirect('catalog_rule/*/');
                return;
            }
        }
        $this->_redirect('catalog_rule/*/');
    }

    /**
     * @param $ruleId
     * @param $value
     *
     * @return void
     */
    public function setIsActiveRules($ruleId, $value){
        if ($ruleId){
            try {
                $connection = $this->resource->getConnection();
                $tableName = "catalogrule";
                $connection->update($tableName,
                    [
                        'is_active' => $value
                    ],
                    [
                        'rule_id IN (?)'    => $ruleId,
                    ]
                );
            }catch (\Exception $e) {
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->error($e->getMessage());
            }
        }
    }
}