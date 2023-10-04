<?php
namespace Ecommage\BannerManager\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

use Ecommage\BannerManager\Api\BannerRepositoryInterface;
use Ecommage\BannerManager\Api\Data\BannerInterface;
use Ecommage\BannerManager\Model\BannerFactory;
use Ecommage\BannerManager\Model\ResourceModel\Banner as BannerResourceModel;
use Ecommage\BannerManager\Model\ResourceModel\Banner\CollectionFactory;

/**
 * Class BannerRepository
 */
class BannerRepository implements BannerRepositoryInterface
{
    protected $bannerFactory;
    protected $bannerResourceModel;
    protected $collectionFactory;
    protected $searchResultsFactory;

    /**
     * BannerRepository constructor.
     *
     * @param BannerFactory $bannerFactory
     * @param BannerResourceModel $bannerResourceModel
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        BannerFactory $bannerFactory,
        BannerResourceModel $bannerResourceModel,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->bannerFactory        = $bannerFactory;
        $this->bannerResourceModel  = $bannerResourceModel;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritDoc
     *
     * @throws CouldNotSaveException
     */
    public function save(BannerInterface $banner)
    {
        try {
            $this->bannerResourceModel->save($banner);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $banner;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        $banner = $this->bannerFactory->create();
        $this->bannerResourceModel->load($banner, $id);
        if (!$banner->getId()) {
            throw new NoSuchEntityException(__('Banner with id "%1" does not exist.', $id));
        }
        return $banner;
    }

    /**
     * @inheritDoc
     */
    public function delete(BannerInterface $banner)
    {
        try {
            $this->bannerResourceModel->delete($banner);
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
        $banners = [];
        foreach ($collection as $bannerModel) {
            $banners[] = $bannerModel;
        }
        $searchResults->setItems($banners);
        return $searchResults;
    }
}
