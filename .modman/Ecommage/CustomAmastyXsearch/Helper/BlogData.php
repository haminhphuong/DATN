<?php
// phpcs:disable Magento2.PHP.ReturnValueCheck.ImproperValueTesting

namespace Ecommage\CustomAmastyXsearch\Helper;

use Amasty\Blog\Model\PostsFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Amasty\Blog\Model\ResourceModel\Posts\CollectionFactory;

/**
 * Class BlogData
 *
 * @package Ecommage\CustomAmastyXsearch\Helper
 */
class BlogData extends AbstractHelper
{
    const ECOMMAGE_SEARCH_SALE = 'search/general/search_sale';
    /**
     * @var PostsFactory
     */
    protected $postsFactory;

    /**
     * @var CollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * BlogData constructor.
     *
     * @param Context $context
     * @param PostsFactory $postsFactory
     * @param CollectionFactory $postCollectionFactory
     */
    public function __construct(
        Context $context,
        PostsFactory $postsFactory,
        CollectionFactory $postCollectionFactory
    )
    {
        $this->postsFactory = $postsFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /**
     * @return string
     */
    public function getStatusModule()
    {
        return $this->getConfig(self::ECOMMAGE_SEARCH_SALE);
    }

    /**
     * @param $urlKey
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostListImgSrcByUrlKey($urlKey)
    {
        $collectionFactory = $this->postCollectionFactory->create();
        $post = $collectionFactory->addFieldToFilter('url_key', $urlKey)->getFirstItem();
        $postId = $post->getId();

        $postFactory = $this->postsFactory->create();
        return $postFactory->load($postId)->getListThumbnailSrc();
    }

    /**
     * @param $str
     *
     * @return mixed|string
     */
    public function getPostUrlKey($str)
    {
        if (strpos($str, '.html')) {
            $str = str_replace('.html', '', $str);
        }
        $arrStr = explode('/', $str);
        return end($arrStr);

    }

    /**
     * @param $str
     * @return string
     */
    public function getUrlBlog($str)
    {
        $urlKey = $this->_urlBuilder->getBaseUrl() . substr($str, 1);
        return $urlKey;
    }
}
