<?php
namespace Ecommage\Backend\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

/**
 * Class ScheduleDesignUpdateMetaProvider customizes Schedule Design Update panel
 *
 * @api
 * @since 101.0.0
 */
class ScheduleChangeDesignUpdate extends AbstractModifier
{
    /**#@+
     * Field names
     */
    const SCHEDULE_CHANGE_START = 'schedule_change_start';
    const SCHEDULE_CHANGE_END = 'schedule_change_end';
    /**#@-*/

    /**
     * @var ArrayManager
     * @since 101.0.0
     */
    protected $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(ArrayManager $arrayManager)
    {
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     *
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        return $this->customizeDateRangeField($meta);
    }

    /**
     * @inheritdoc
     *
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Customize date range field if from and to fields belong to one group
     *
     * @param array $meta
     * @return array
     * @since 101.0.0
     */
    protected function customizeDateRangeField(array $meta)
    {
        if ($this->getGroupCodeByField($meta, self::SCHEDULE_CHANGE_START)
            !== $this->getGroupCodeByField($meta, self::SCHEDULE_CHANGE_END)
        ) {
            return $meta;
        }

        $fromFieldPath = $this->arrayManager->findPath(self::SCHEDULE_CHANGE_START, $meta, null, 'children');
        $toFieldPath = $this->arrayManager->findPath(self::SCHEDULE_CHANGE_END, $meta, null, 'children');
        $fromContainerPath = $this->arrayManager->slicePath($fromFieldPath, 0, -2);
        $toContainerPath = $this->arrayManager->slicePath($toFieldPath, 0, -2);

        $meta = $this->arrayManager->merge(
            $fromFieldPath . self::META_CONFIG_PATH,
            $meta,
            [
                'label' => __('Schedule Change From'),
                'additionalClasses' => 'admin__field-date',
            ]
        );
        $meta = $this->arrayManager->merge(
            $toFieldPath . self::META_CONFIG_PATH,
            $meta,
            [
                'label' => __('To'),
                'scopeLabel' => null,
                'additionalClasses' => 'admin__field-date',
            ]
        );
        $meta = $this->arrayManager->merge(
            $fromContainerPath . self::META_CONFIG_PATH,
            $meta,
            [
                'label' => false,
                'required' => false,
                'additionalClasses' => 'admin__control-grouped-date',
                'breakLine' => false,
                'component' => 'Magento_Ui/js/form/components/group',
            ]
        );
        $meta = $this->arrayManager->set(
            $fromContainerPath . '/children/' . self::SCHEDULE_CHANGE_END,
            $meta,
            $this->arrayManager->get($toFieldPath, $meta)
        );

        return $this->arrayManager->remove($toContainerPath, $meta);
    }
}
