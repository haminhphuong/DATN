<?php

namespace Ecommage\BannerManager\Model\ResourceModel;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\EntityMetadata;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Ecommage\BannerManager\Api\Data\BannerInterface;
use Ecommage\BannerManager\Api\Data\ItemInterface;
use Ecommage\BannerManager\Model\ResourceModel\Banner\Item\CollectionFactory;
use Ecommage\BannerManager\Model\System\Config\Status;

/**
 * Class Banner
 */
class Banner extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $_dateTime;
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var CollectionFactory
     */
    protected $itemCollectionFactory;

    /**
     * Banner constructor.
     *
     * @param Context               $context
     * @param CollectionFactory     $itemCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param EntityManager         $entityManager
     * @param MetadataPool          $metadataPool
     * @param null                  $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        CollectionFactory $itemCollectionFactory,
        StoreManagerInterface $storeManager,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {

        $this->_dateTime             = $dateTime;
        $this->_storeManager         = $storeManager;
        $this->entityManager         = $entityManager;
        $this->metadataPool          = $metadataPool;
        $this->itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($context, $connectionName);
    }

    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init('banner_entity', BannerInterface::BANNER_ID);
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(BannerInterface::class)->getEntityConnection();
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|DataObject $object
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $options = $object->getOptions();
        if (is_array($options)) {
            $object->setData('options', json_encode($options));
        }

        if (empty($object->getIdentifier())) {
            $identifier = $this->generateIdentifier($object->getTitle());
            $object->setIdentifier($identifier);
        }

        if (!$this->getIsUniqueBannerToStores($object)) {
            throw new LocalizedException(
                __('A banner identifier with the same properties already exists in the selected store.')
            );
        }

        return $this;
    }

    /**
     * @param $title
     *
     * @return string|string[]|null
     */
    private function generateIdentifier($title)
    {
        return preg_replace('#[^0-9a-z]+#i', '-', strtolower($title));
    }

    /**
     * Get block id.
     *
     * @param AbstractModel $object
     * @param mixed         $value
     * @param string        $field
     *
     * @return bool|int|string
     * @throws LocalizedException
     * @throws Exception
     */
    private function getBannerId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }

        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                   ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                   ->limit(1);
            $result   = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }

        return $entityId;
    }

    /**
     * Load an object
     *
     * @param \Ecommage\BannerManager\Model\Banner|AbstractModel $object
     * @param mixed                                              $value
     * @param string                                             $field field to load by (defaults to model id)
     *
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $bannerId = $this->getBannerId($object, $value, $field);
        if ($bannerId) {
            $this->entityManager->load($object, $bannerId);
        }
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string                                             $field
     * @param mixed                                              $value
     * @param \Ecommage\BannerManager\Model\Banner|AbstractModel $object
     *
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField      = $entityMetadata->getLinkField();
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];
            $select->join(
                ['bes' => $this->getTable('banner_store')],
                $this->getMainTable() . '.' . $linkField . ' = bes.' . $linkField,
                ['store_id']
            )->where('is_active = ?', 1)
             ->where('bes.store_id in (?)', $stores)
             ->order('store_id DESC')
             ->limit(1);
        }

        return $select;
    }

    /**
     * Check for unique of identifier of banner to selected store(s).
     *
     * @param AbstractModel $object
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUniqueBannerToStores(AbstractModel $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField      = $entityMetadata->getLinkField();
        $stores         = (array)$object->getData('store_id');
        $isDefaultStore = $this->_storeManager->isSingleStoreMode()
                          || array_search(Store::DEFAULT_STORE_ID, $stores) !== false;

        if (!$isDefaultStore) {
            $stores[] = Store::DEFAULT_STORE_ID;
        }

        $select = $this->getConnection()->select()
                       ->from(['cb' => $this->getMainTable()])
                       ->join(
                           ['bes' => $this->getTable('banner_store')],
                           'cb.' . $linkField . ' = bes.' . $linkField,
                           []
                       )
                       ->where('cb.identifier = ?  ', $object->getData('identifier'));

        if (!$isDefaultStore) {
            $select->where('bes.store_id IN (?)', $stores);
        }

        if ($object->getId()) {
            $select->where('cb.' . $entityMetadata->getIdentifierField() . ' <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     *
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField      = $entityMetadata->getLinkField();

        $select = $connection->select()
                             ->from(['bes' => $this->getTable('banner_store')], 'store_id')
                             ->join(
                                 ['cb' => $this->getMainTable()],
                                 'bes.' . $linkField . ' = cb.' . $linkField,
                                 []
                             )
                             ->where('cb.' . $entityMetadata->getIdentifierField() . ' = :banner_id');

        return $connection->fetchCol($select, [BannerInterface::BANNER_ID => (int)$id]);
    }

    /**
     * Save an object.
     *
     * @param AbstractModel $object
     *
     * @return $this
     * @throws Exception
     */
    public function save(AbstractModel $object)
    {
        $this->beforeSave($object);
        $this->entityManager->save($object);
        $this->afterSave($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return Banner\Item\Collection|null
     * @throws Exception
     */
    public function getBannerItems(AbstractModel $object)
    {
        $value = $object->getId();
        if ($value) {
            /** @var EntityMetadata $entityMetadata */
            $entityMetadata = $this->metadataPool->getMetadata(ItemInterface::class);
            $field          = $entityMetadata->getIdentifierField();
            $collection     = $this->itemCollectionFactory->create();
            $collection->addFieldToFilter($field, $value);
            $collection->setOrder(ItemInterface::POSITION, 'ASC');
            if ($object->getVisibilityFilter()) {
                $currentDate = $this->_dateTime->gmtDate('Y-m-d H:i:s');
                $collection->addFieldToFilter(ItemInterface::IS_ACTIVE, Status::STATUS_ENABLED);
                $collection->addFieldToFilter(
                    [
                        ItemInterface::END_DATE,
                        ItemInterface::END_DATE
                    ],
                    [
                        ['gteq' => $currentDate],
                        ['null' => true]
                    ]
                );
                $collection->addFieldToFilter(
                    [
                        ItemInterface::START_DATE,
                        ItemInterface::START_DATE
                    ],
                    [
                        ['lteq' => $currentDate],
                        ['null' => true]
                    ]
                );
            }

            return $collection;
        }

        return null;
    }
}
