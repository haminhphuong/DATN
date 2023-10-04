<?php
namespace Ecommage\WidgetRecentlyView\Setup\Patch\Data;

use Amasty\Mostviewed\Helper\Config;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddPageSale implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page
     */
    private $pageRepository;

    /**
     * AddNewCmsPage constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PageFactory $pageFactory
     * @param \Magento\Cms\Model\ResourceModel\Page $pageResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PageFactory $pageFactory,
        PageRepositoryInterface $pageRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $pageTitle = "Sale";
        $pageIdentifier = "sale";
        $pageContent = '<style>#html-body [data-pb-style=LCBWN82],#html-body [data-pb-style=QGSVJHO]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=LCBWN82]{width:66.6667%;align-self:stretch}#html-body [data-pb-style=T4MTN77]{width:33.3333%;align-self:stretch}#html-body [data-pb-style=C1113SV],#html-body [data-pb-style=DU0SVU4],#html-body [data-pb-style=QIBLQDL],#html-body [data-pb-style=T4MTN77]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="QGSVJHO"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="LCBWN82"></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="T4MTN77"><div data-content-type="block" data-appearance="default" data-element="main"></div></div></div></div></div><div data-content-type="block" data-appearance="default" data-element="main"></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="QIBLQDL"></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="DU0SVU4"></div></div><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="C1113SV"><div data-content-type="text" data-appearance="default" data-element="main"><p>{{widget type="NiceForNow\RecentlyProduct\Block\RecentlyViewed" title="recently" page_size="8" display_type="random_products" show_buttons="" show_template="product/recently_view_grid_product.phtml"}}</p></div></div></div>';

        $page = $this->pageFactory->create();
        $page->setTitle($pageTitle)
            ->setIdentifier($pageIdentifier)
            ->setIsActive(1)
            ->setPageLayout('cms-full-width')
            ->setStoreId(["0"])
            ->setContent($pageContent)
            ->save();

        $this->moduleDataSetup->startSetup();
        $this->pageRepository->save($page);
        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
