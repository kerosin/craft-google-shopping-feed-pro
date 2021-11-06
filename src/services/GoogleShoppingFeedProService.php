<?php
/**
 * Google Shopping Feed Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\googleshoppingfeedpro\services;

use kerosin\googleshoppingfeedpro\GoogleShoppingFeedPro;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;
use craft\elements\Entry;
use craft\web\View;

use DateTime;
use Exception;

/**
 * @author    kerosin
 * @package   GoogleShoppingFeedPro
 * @since     1.0.0
 */
class GoogleShoppingFeedProService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * @param mixed $criteria
     * @return Entry[]
     * @throws Exception
     */
    public function getFeedEntries($criteria = null): array
    {
        if (!empty($criteria)) {
            $result = Entry::findAll($criteria);
        } else {
            $result = Entry::find()
                ->site(Craft::$app->getSites()->getCurrentSite())
                ->status(Entry::STATUS_LIVE)
                ->all();
        }

        return $result;
    }

    /**
     * @param mixed $criteria
     * @return Product[]
     * @throws Exception
     */
    public function getFeedProducts($criteria = null): array
    {
        $result = [];

        if (!Craft::$app->getPlugins()->isPluginInstalled('commerce')) {
            return $result;
        }

        if (!empty($criteria)) {
            $result = Product::findAll($criteria);
        } else {
            $result = Product::find()
                ->site(Craft::$app->getSites()->getCurrentSite())
                ->status(Product::STATUS_LIVE)
                ->all();
        }

        return $result;
    }

    /**
     * @param Element[] $elements
     * @return string
     * @throws Exception
     */
    public function getFeedXml(array $elements): string
    {
        if (Craft::$app->getPlugins()->getPluginInfo('google-shopping-feed-pro')['isTrial']) {
            $elements = array_slice($elements, 0, 10);
        }

        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return Craft::$app->getView()->renderTemplate(
            'google-shopping-feed-pro/_feed',
            [
                'elements' => $elements,
                'settings' => $settings,
            ],
            View::TEMPLATE_MODE_CP
        );
    }

    /**
     * @param mixed $criteria
     * @return string
     * @throws Exception
     */
    public function getEntriesFeedXml($criteria = null): string
    {
        return $this->getFeedXml($this->getFeedEntries($criteria));
    }

    /**
     * @param mixed $criteria
     * @return string
     * @throws Exception
     */
    public function getProductsFeedXml($criteria = null): string
    {
        return $this->getFeedXml($this->getFeedProducts($criteria));
    }

    /**
     * @param Element[] $elements
     * @return void
     * @throws Exception
     * @since 1.3.0
     */
    public function generateFeed(array $elements): void
    {
        $response = Craft::$app->getResponse();
        $response->getHeaders()->set('Content-Type', 'application/xml; charset=UTF-8');

        echo $this->getFeedXml($elements);
    }

    /**
     * @param Element $element
     * @param string $field
     * @return mixed
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementFieldValue(Element $element, string $field)
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
     * @since 1.3.0
     */
    public function getElementSalesMinStartDate(Element $element): ?DateTime
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
     * @since 1.3.0
     */
    public function getElementSalesMaxEndDate(Element $element): ?DateTime
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
     * @since 1.3.0
     */
    public function isCustomValue(?string $value): bool
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return $value == $settings::OPTION_CUSTOM_VALUE;
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseProductId(?string $value): bool
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return $value == $settings::OPTION_USE_PRODUCT_ID;
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseSaleStartDate(?string $value): bool
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return $value == $settings::OPTION_USE_SALE_START_DATE;
    }

    /**
     * @param string $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseSaleEndDate(?string $value): bool
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        return $value == $settings::OPTION_USE_SALE_END_DATE;
    }
}
