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
     * @param string|null $field
     * @param mixed $customValue
     * @return mixed
     * @throws Exception
     */
    public function elementFieldValue(Element $element, ?string $field, $customValue = null)
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementFieldValue($element, $field, $customValue);
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
     * @param Element $element
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function elementUrl(Element $element): ?string
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->getElementUrl($element);
    }

    /**
     * @param Element $element
     * @return mixed
     * @throws Exception
     * @since 1.3.0
     */
    public function elementAvailabilityFieldValue(Element $element)
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementAvailabilityFieldValue($element);
    }

    /**
     * @param Element $element
     * @return mixed
     * @throws Exception
     * @since 1.3.0
     */
    public function elementItemGroupIdFieldValue(Element $element)
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementItemGroupIdFieldValue($element);
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @param mixed $customValue
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function elementCurrencyIso(Element $element, ?string $field, $customValue = null): ?string
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementCurrencyIso($element, $field, $customValue);
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @param mixed $customValue
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function elementDimensionUnit(Element $element, ?string $field, $customValue = null): ?string
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementDimensionUnit($element, $field, $customValue);
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @param mixed $customValue
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function elementWeightUnit(Element $element, ?string $field, $customValue = null): ?string
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementWeightUnit($element, $field, $customValue);
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function elementSaleStartDate(Element $element, ?string $field): ?string
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementSaleStartDate($element, $field);
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function elementSaleEndDate(Element $element, ?string $field): ?string
    {
        return GoogleShoppingFeedPro::$plugin
            ->googleShoppingFeedProService
            ->getElementSaleEndDate($element, $field);
    }

    /**
     * @param string|null $value
     * @return bool
     */
    public function isCustomValue(?string $value): bool
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->isCustomValue($value);
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.1.0
     * @deprecated in 1.3.0
     */
    public function isUseProductId(?string $value): bool
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->isUseProductId($value);
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.2.0
     * @deprecated in 1.3.0
     */
    public function isUseSaleStartDate(?string $value): bool
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->isUseSaleStartDate($value);
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.2.0
     * @deprecated in 1.3.0
     */
    public function isUseSaleEndDate(?string $value): bool
    {
        return GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService->isUseSaleEndDate($value);
    }
}
