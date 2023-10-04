<?php

namespace Ecommage\RelatedCategory\Model\ResourceModel;

use Magento\Catalog\Model\Category;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class CategoryPosts
 *
 * @package Ecommage\RelatedCategory\Model\ResourceModel
 */
class CategorySlider extends AbstractDb
{
    /**
     * Construct
     *
     * @param Context $context
     * @param string                                            $connectionName
     */
    public function __construct(
        Context $context,
                $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('category_assign_slider', 'id');
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    public function getPostByCategory(Category $category)
    {
        $conn     = $this->getConnection();
        $storeTbl = $this->getTable('category_assign_slider');
        $select   = $conn->select();
        $select->from(
            $storeTbl,
            ['slider_ctg_id']
        )->where(
            'category_id = (?)',
            $category->getId()
        );

        return $conn->fetchCol($select);
    }

    /**
     * @param Category $category
     * @param                          $postIds
     *
     * @return $this|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function updatePostForCategory(Category $category, $postIds)
    {
        $conn     = $this->getConnection();
        $storeTbl = $this->getTable('category_assign_slider');
        $oldPosts = [];
        /* Delete exist resources*/
        if ($category->getId()) {
            $select = $conn->select();
            $select->from(
                $storeTbl,
                ['slider_ctg_id']
            )->where(
                'category_id = (?)',
                $category->getId()
            );

            $oldPosts = $conn->fetchCol($select);
        }

        /*Insert new resources*/
        if (!$postIds || !is_array($postIds) || !count($postIds)) {
            $postIds = [];
        }

        $insert = array_diff($postIds, $oldPosts);
        $delete = array_diff($oldPosts, $postIds);

        /**
         * Delete experience from cv
         */
        if (!empty($delete)) {
            $cond = ['slider_ctg_id IN(?)' => array_values($delete), 'category_id=?' => $category->getId()];
            $conn->delete($storeTbl, $cond);
        }

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $postId) {
                $data[] = ['cv_id' => $category->getId(), 'slider_ctg_id' => (int)$postId];
            }
            $conn->insertArray($storeTbl, ['category_id', 'slider_ctg_id'], $data);
        }
        return $this;
    }
}
