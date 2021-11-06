<?php
/**
 * Google Shopping Feed Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\googleshoppingfeedpro\variables;

use kerosin\googleshoppingfeedpro\GoogleShoppingFeedPro;

use craft\base\Element;

use DateTime;
use Exception;

/**
 * @author    kerosin
 * @package   GoogleShoppingFeedPro
 * @since     1.0.0
 */
class GoogleShoppingFeedProVariable
{
    // Public Methods
    // =========================================================================

    /**
     * @param Element[] $elements
     * @return void
     * @throws Exception
     */
    public function generateFeed(array $elements): void
    {
        GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->generateFeed($elements);
    }

    /**
     * @param Element $element
     * @param string $field
     * @return mixed
     * @throws Exception
     */
    public function elementFieldValue(Element $element, string $field)
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementFieldValue($element, $field);
    }

    /**
     * @param Element $element
     * @return DateTime|null
     * @since 1.2.0
     */
    public function elementSalesMinStartDate(Element $element): ?DateTime
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementSalesMinStartDate($element);
    }

    /**
     * @param Element $element
     * @return DateTime|null
     * @since 1.2.0
     */
    public function elementSalesMaxEndDate(Element $element): ?DateTime
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementSalesMaxEndDate($element);
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isCustomValue(?string $value): bool
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->isCustomValue($value);
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.1.0
     */
    public function isUseProductId(?string $value): bool
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->isUseProductId($value);
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.2.0
     */
    public function isUseSaleStartDate(?string $value): bool
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->isUseSaleStartDate($value);
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.2.0
     */
    public function isUseSaleEndDate(?string $value): bool
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->isUseSaleEndDate($value);
    }
}
