<?php
/**
 * Google Shopping Feed Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\googleshoppingfeedpro\variables;

use kerosin\googleshoppingfeedpro\GoogleShoppingFeedPro;
use kerosin\googleshoppingfeedpro\services\GoogleShoppingFeedProService;

use Craft;
use craft\base\Element;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;

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
     * @param Element $element
     * @param string $field
     * @return mixed
     * @throws Exception
     */
    public function elementFieldValue(Element $element, string $field)
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();
        $object = $element;

        if (Craft::$app->getPlugins()->isPluginInstalled('commerce')) {
            if ($element instanceof Product) {
                if (isset($element->getDefaultVariant()->$field)) {
                    $object = $element->getDefaultVariant();
                }
            } elseif ($element instanceof Variant) {
                $product = $element->getProduct();

                if (
                    !isset($element->$field) &&
                    $settings->useProductData &&
                    $product != null &&
                    isset($product->$field)
                ) {
                    $object = $element->getProduct();
                }
            }
        }

        return isset($object->$field) ? $object->$field : null;
    }

    /**
     * @param Element $element
     * @return DateTime|null
     * @since 1.2.0
     */
    public function elementSalesMinStartDate(Element $element): ?DateTime
    {
        $result = null;

        if (
            !Craft::$app->getPlugins()->isPluginInstalled('commerce') ||
            !($element instanceof Product || $element instanceof Variant)
        ) {
            return $result;
        }

        if ($element instanceof Product) {
            $sales = $element->getDefaultVariant()->getSales();
        } else {
            $sales = $element->getSales();
        }

        if ($sales == null || count($sales) == 0) {
            return $result;
        }

        foreach ($sales as $sale) {
            if ($sale->dateFrom == null) {
                $result = null;
                break;
            }

            if ($result == null || $sale->dateFrom < $result) {
                $result = $sale->dateFrom;
            }
        }

        return $result;
    }

    /**
     * @param Element $element
     * @return DateTime|null
     * @since 1.2.0
     */
    public function elementSalesMaxEndDate(Element $element): ?DateTime
    {
        $result = null;

        if (
            !Craft::$app->getPlugins()->isPluginInstalled('commerce') ||
            !($element instanceof Product || $element instanceof Variant)
        ) {
            return $result;
        }

        if ($element instanceof Product) {
            $sales = $element->getDefaultVariant()->getSales();
        } else {
            $sales = $element->getSales();
        }

        if ($sales == null || count($sales) == 0) {
            return $result;
        }

        foreach ($sales as $sale) {
            if ($sale->dateTo == null) {
                $result = null;
                break;
            }

            if ($result == null || $sale->dateTo > $result) {
                $result = $sale->dateTo;
            }
        }

        return $result;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isCustomValue(?string $value): bool
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return $value == $settings::OPTION_CUSTOM_VALUE;
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.1.0
     */
    public function isUseProductId(?string $value): bool
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return $value == $settings::OPTION_USE_PRODUCT_ID;
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.2.0
     */
    public function isUseSaleStartDate(?string $value): bool
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return $value == $settings::OPTION_USE_SALE_START_DATE;
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.2.0
     */
    public function isUseSaleEndDate(?string $value): bool
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return $value == $settings::OPTION_USE_SALE_END_DATE;
    }

    /**
     * @param Element[] $elements
     * @return void
     * @throws Exception
     */
    public function generateFeed(array $elements): void
    {
        $response = Craft::$app->getResponse();
        $response->getHeaders()->set('Content-Type', 'application/xml; charset=UTF-8');

        /** @var GoogleShoppingFeedProService $googleShoppingFeedProService */
        $googleShoppingFeedProService = GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService;

        echo $googleShoppingFeedProService->getFeedXml($elements);
    }
}
