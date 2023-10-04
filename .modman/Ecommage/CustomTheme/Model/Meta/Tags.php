<?php

namespace Ecommage\CustomTheme\Model\Meta;

use Amasty\Blog\Api\PostRepositoryInterface;
use Amasty\Blog\Model\Image\ImagePathConverter;
use Ecommage\OpenGraph\Helper\Data;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Page\Config;
use Magento\Theme\Block\Html\Header\Logo;

class Tags extends \Ecommage\OpenGraph\Block\Meta\Tags
{
    /**
     * @var ImagePathConverter
     */
    protected $imagePathConverter;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * Tags constructor.
     *
     * @param Registry                        $registry
     * @param Logo                            $logo
     * @param PostRepositoryInterface         $postRepository
     * @param ImagePathConverter              $imagePathConverter
     * @param Data $helperData
     * @param Config                          $pageConfig
     * @param Resolver                        $localeResolver
     * @param Context                         $context
     * @param array                           $data
     */
    public function __construct(
        Registry $registry,
        Logo $logo,
        PostRepositoryInterface $postRepository,
        ImagePathConverter $imagePathConverter,
        Data $helperData,
        Config $pageConfig,
        Resolver $localeResolver,
        Context $context,
        array $data = []
    ) {
        $this->postRepository     = $postRepository;
        $this->imagePathConverter = $imagePathConverter;
        parent::__construct($registry, $logo, $helperData, $pageConfig, $localeResolver, $context, $data);
    }

    /**
     * @return Template
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $router     = $this->getRequest()->getRouteName();
        $action     = $this->getRequest()->getActionName();
        if ($router == 'amblog') {
            $imgSrc = null;
            if ($action == 'post') {
                $postId = $this->getRequest()->getParam('id');
                $imgSrc = $this->getAmImageInBlog('amasty.blog.content.post');
                if (empty($imgSrc)) {
                    $post   = $this->postRepository->getById($postId);
                    $imgSrc = $this->imagePathConverter->getImagePath($post->getPostThumbnail());
                }
                $this->setData('og_type', 'article');
            } elseif ($action == 'category') {
                $imgSrc = $this->getAmImageInBlog('amblog.content.list.wrapper');
            } elseif ($action == 'index') {
                $imgSrc = $this->getAmImageInBlog('amblog.content.featured');
            }

            if (!empty($imgSrc)) {
                $this->setData('og_image', $imgSrc);
            }
        }

        return $this;
    }
}
