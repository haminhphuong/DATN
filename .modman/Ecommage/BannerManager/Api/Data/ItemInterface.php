<?php

namespace Ecommage\BannerManager\Api\Data;

/**
 * Interface ItemInterface
 *
 * @api
 */
interface ItemInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const BANNER_ITEM_ID = 'entity_id';
    const BANNER_ID      = 'banner_id';
    const TITLE          = 'title';
    const MEDIA_TYPE     = 'media_type';
    const IMAGE_DESKTOP  = 'image_desktop';
    const IMAGE_MOBILE   = 'image_mobile';
    const LINK           = 'link';
    const VIDEO_LINK     = 'video_link';
    const DESCRIPTION    = 'description';
    const OPTIONS        = 'options';
    const CLASS_ITEM     = 'class_item';
    const START_DATE     = 'start_date';
    const END_DATE       = 'end_date';
    const CREATION_TIME  = 'creation_time';
    const UPDATE_TIME    = 'update_time';
    const IS_ACTIVE      = 'is_active';
    const POSITION       = 'position';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Banner ID
     *
     * @return int|null
     */
    public function getBannerId();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get link
     *
     * @return string|null
     */
    public function getLink();

    /**
     * Get image desktop
     *
     * @return string|null
     */
    public function getImageDesktop();

    /**
     * Get image mobile
     *
     * @return string|null
     */
    public function getImageMobile();

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Get options
     *
     * @return array|null
     */
    public function getOptions();

    /**
     * Get class item
     *
     * @return string|null
     */
    public function getClassItem();

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
     * Get start date
     *
     * @return ItemInterface
     */
    public function getStartDate();

    /**
     * Get end date
     *
     * @return ItemInterface
     */
    public function getEndDate();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return ItemInterface
     */
    public function setId($id);

    /**
     * Set Banner ID
     *
     * @param int $id
     *
     * @return ItemInterface
     */
    public function setBannerId($id);

    /**
     * Set Title
     *
     * @param string $title
     *
     * @return ItemInterface
     */
    public function setTitle($title);

    /**
     * Set Image desktop
     *
     * @param string $image
     *
     * @return ItemInterface
     */
    public function setImageDesktop($image);

    /**
     * Set Image mobile
     *
     * @param string $image
     *
     * @return ItemInterface
     */
    public function setImageMobile($image);

    /**
     * Set Link
     *
     * @param string $link
     *
     * @return ItemInterface
     */
    public function setLink($link);

    /**
     * Set Description
     *
     * @param string $description
     *
     * @return ItemInterface
     */
    public function setDescription($description);

    /**
     * Set Options
     *
     * @param array $options
     *
     * @return ItemInterface
     */
    public function setOptions($options);

    /**
     * Set Class item
     *
     * @param string $class
     *
     * @return ItemInterface
     */
    public function setClassItem($class);

    /**
     * Set creation time
     *
     * @param string $creationTime
     *
     * @return ItemInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     *
     * @return ItemInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set start date
     *
     * @param string $date
     *
     * @return ItemInterface
     */
    public function setStartDate($date);

    /**
     * Set end date
     *
     * @param string $date
     *
     * @return ItemInterface
     */
    public function setEndDate($date);

    /**
     * Set is active
     *
     * @param bool|int $isActive
     *
     * @return ItemInterface
     */
    public function setIsActive($isActive);

    /**
     * Set position
     *
     * @param int $position
     *
     * @return ItemInterface
     */
    public function setPosition($position);
}
