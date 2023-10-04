<?php

namespace Ecommage\CustomAmastyXsearch\Block\MostSearched;

use Amasty\Xsearch\Model\QueryInfo;
use Amasty\Xsearch\Model\ResourceModel\UserSearch\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class ListKeySearch extends Template implements BlockInterface
{
    const DEFAULT_LIMIT = 20;
    /**
     * @var QueryInfo
     */
    protected $queryInfo;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    public function __construct(
        Template\Context $context, QueryInfo $queryInfo,
        CollectionFactory $collectionFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->queryInfo         = $queryInfo;
        $this->collectionFactory = $collectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    public function getCollectionMostKeySearch()
    {
        $data              = $this->getData();
        $data['key_count'] = isset($data['key_count']) ? $data['key_count'] : self::DEFAULT_LIMIT;

        $mostWanted = $this->getMostWantedQueries($data['key_count'], 1);

        return $mostWanted;
    }

    /**
     * @return array
     */
    public function getMostWantedQueries($limit = null, $curPage = 1)
    {
        $queryCollection = $this->collectionFactory;
        $searchQueries   = $queryCollection->create()
                                           ->setCurPage($curPage)
                                           ->setPageSize($limit)
                                           ->getSearchQueries($limit);
        $result = [];
        $temp = 0;
        $index = 0;

        if ($searchQueries->getSize()){
            foreach ($searchQueries->getData() as $item){
                $result[$index][] = $item;
                $temp++;
                if ($temp %2 == 0){
                    $index++;
                }
            }
        }

        return $result;
    }

}
