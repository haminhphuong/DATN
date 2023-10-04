<?php

namespace Ecommage\BannerManager\Api\Data;

/**
 * Interface BannerInterface
 *
 * @api
 */
interface BannerInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const BANNER_ID     = 'banner_id';
    const IDENTIFIER    = 'identifier';
    const TITLE         = 'title';
    const TYPE_ID       = 'type_id';
    const OPTIONS       = 'options';
    const BANNER_ITEMS  = 'banner_items';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME   = 'update_time';
    const IS_ACTIVE     = 'is_active';

    const FORM_GENERAL = 'banner';
    const FORM_OPTIONS = 'options';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Type ID
     *
     * @return int
     */
    public function getTypeId();

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get options
     *
     * @return array|null
     */
    public function getOptions();

    /**
     * Get banner items
     *
     * @return array|null
     */
    public function getBannerItems();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return BannerInterface
     */
    public function setId($id);

    /**
     * Set Type ID
     *
     * @param int $id
     *
     * @return BannerInterface
     */
    public function setTypeId($id);

    /**
     * Set identifier
     *
     * @param string $identifier
     *
     * @return BannerInterface
     */
    public function setIdentifier($identifier);

    /**
     * Set title
     *
     * @param string $title
     *
     * @return BannerInterface
     */
    public function setTitle($title);

    /**
     * Set options
     *
     * @param null|array $options
     *
     * @return BannerInterface
     */
    public function setOptions(array $options = null);

    /**
     * Set banner items
     *
     * @param null|array $items
     *
     * @return BannerInterface
     */
    public function setBannerItems(array $items = null);

    /**
     * Set creation time
     *
     * @param string $creationTime
     *
     * @return BannerInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     *
     * @return BannerInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set is active
     *
     * @param bool|int $isActive
     *
     * @return BannerInterface
     */
    public function setIsActive($isActive);
}
