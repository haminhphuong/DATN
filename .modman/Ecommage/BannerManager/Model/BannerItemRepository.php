<?php
namespace Ecommage\BannerManager\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Ecommage\BannerManager\Api\BannerItemRepositoryInterface;
use Ecommage\BannerManager\Api\Data\ItemInterface;
use Ecommage\BannerManager\Model\Banner\ItemFactory;
use Ecommage\BannerManager\Model\ResourceModel\Banner\Item as ItemResourceModel;
use Ecommage\BannerManager\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Class BannerRepository
 */
class BannerItemRepository implements BannerItemRepositoryInterface
{
    protected $bannerItemFactory;
    protected $bannerItemResourceModel;
    protected $collectionFactory;
    protected $searchResultsFactory;

    /**
     * BannerRepository constructor.
     *
     * @param ItemFactory $bannerItemFactory
     * @param ItemResourceModel $bannerItemResourceModel
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ItemFactory $bannerItemFactory,
        ItemResourceModel $bannerItemResourceModel,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->bannerItemFactory        = $bannerItemFactory;
        $this->bannerItemResourceModel  = $bannerItemResourceModel;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     *
     * @throws CouldNotSaveException
     */
    public function save(ItemInterface $item)
    {
        try {
            $this->bannerItemResourceModel->save($item);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        $bannerItem = $this->bannerItemFactory->create();
        $this->bannerItemResourceModel->load($bannerItem, $id);
        if (!$bannerItem->getId()) {
            throw new NoSuchEntityException(__('Banner Item with id "%1" does not exist.', $id));
        }
        return $bannerItem;
    }

    /**
     * @inheritDoc
     */
    public function delete(ItemInterface $item)
    {
        try {
            $this->bannerItemResourceModel->delete($item);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $bannerItems = [];
        foreach ($collection as $bannerItemModel) {
            $bannerItems[] = $bannerItemModel;
        }
        $searchResults->setItems($bannerItems);
        return $searchResults;
    }
}
