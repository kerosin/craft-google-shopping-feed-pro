<?php
/**
 * Google Shopping Feed Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\googleshoppingfeedpro\services;

use kerosin\googleshoppingfeedpro\GoogleShoppingFeedPro;
use kerosin\googleshoppingfeedpro\models\Settings;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\commerce\elements\Product;
use craft\commerce\elements\Variant;
use craft\commerce\Plugin as CommercePlugin;
use craft\elements\db\AssetQuery;
use craft\elements\db\ElementQuery;
use craft\elements\db\UserQuery;
use craft\elements\Entry;
use craft\fields\data\OptionData;
use craft\helpers\ArrayHelper;
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

        if (!$this->isCommerceInstalled()) {
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

        return Craft::$app->getView()->renderTemplate(
            'google-shopping-feed-pro/_feed',
            [
                'elements' => $elements,
                'settings' => $this->getSettings(),
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
     * @param string|null $field
     * @param mixed $customValue
     * @return mixed
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementFieldValue(Element $element, ?string $field, $customValue = null)
    {
        $result = null;

        if ($field == null) {
            return $result;
        }

        if ($this->isCustomValue($field)) {
            return $customValue;
        }

        $object = $element;

        if ($this->isCommerceInstalled()) {
            if ($element instanceof Product) {
                if (isset($element->getDefaultVariant()->{$field})) {
                    $object = $element->getDefaultVariant();
                }
            } elseif ($element instanceof Variant) {
                $product = $element->getProduct();

                if (
                    !isset($element->{$field}) &&
                    $this->getSettings()->useProductData &&
                    $product != null &&
                    isset($product->{$field})
                ) {
                    $object = $element->getProduct();
                }
            }
        }

        if (!isset($object->{$field})) {
            return $result;
        }

        $value = $object->{$field};

        if ($value instanceof DateTime) {
            $result = $value->format(DateTime::ATOM);
        } elseif ($value instanceof AssetQuery) {
            $items = $value->all();

            if (count($items)) {
                $values = [];

                foreach ($items as $item) {
                    if ($item->getUrl() != null) {
                        $values[] = $item->getUrl();
                    }
                }

                if (count($values)) {
                    $result = $values;
                }
            }
        } elseif ($value instanceof UserQuery) {
            $items = $value->all();

            if (count($items)) {
                $values = [];

                foreach ($items as $item) {
                    if ($item->username != null) {
                        $values[] = $item->username;
                    }
                }

                if (count($values)) {
                    $result = $values;
                }
            }
        } elseif ($value instanceof ElementQuery) {
            $items = $value->all();

            if (count($items)) {
                $values = [];

                foreach ($items as $item) {
                    if (isset($item->title) && $item->title != '') {
                        $values[] = $item->title;
                    }
                }

                if (count($values)) {
                    $result = $values;
                }
            }
        } elseif (ArrayHelper::isTraversable($value)) {
            if (count($value)) {
                $values = [];

                foreach ($value as $item) {
                    if ($item instanceof OptionData && isset($item->label) && $item->label != '') {
                        $values[] = $item->label;
                    } elseif ($item != null) {
                        $values[] = (string)$item;
                    }
                }

                if (count($values)) {
                    $result = $values;
                }
            }
        } elseif ($value instanceof OptionData) {
            if (isset($value->label) && $value->label != '') {
                $result = $value->label;
            } else {
                $result = (string)$value;
            }
        } else {
            $result = $value;
        }

        return $result;
    }

    /**
     * @param Element $element
     * @return DateTime|null
     * @since 1.3.0
     */
    public function getElementSalesMinStartDate(Element $element): ?DateTime
    {
        $result = null;

        if (!($element instanceof Product || $element instanceof Variant)) {
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

        if (!($element instanceof Product || $element instanceof Variant)) {
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
     * @param Element $element
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementUrl(Element $element): ?string
    {
        $result = $element->getUrl();

        if (
            $element instanceof Variant &&
            $this->getSettings()->useProductUrl &&
            $element->getProduct() != null
        ) {
            $result = $element->getProduct()->getUrl();
        }

        return $result;
    }

    /**
     * @param Element $element
     * @return mixed
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementAvailabilityFieldValue(Element $element)
    {
        $settings = $this->getSettings();
        $result = $settings::AVAILABILITY_IN_STOCK;

        if ($this->isUseStockField($settings->availabilityField)) {
            if ($element instanceof Product) {
                $result = $element->getDefaultVariant()->hasStock()
                    ? $settings::AVAILABILITY_IN_STOCK
                    : $settings::AVAILABILITY_OUT_OF_STOCK;
            } elseif ($element instanceof Variant) {
                $result = $element->hasStock()
                    ? $settings::AVAILABILITY_IN_STOCK
                    : $settings::AVAILABILITY_OUT_OF_STOCK;
            }
        } elseif ($settings->availabilityField != null) {
            $result = $this->getElementFieldValue(
                $element,
                $settings->availabilityField,
                $settings->availabilityCustomValue
            ) ?: $settings::AVAILABILITY_IN_STOCK;
        }

        return $result;
    }

    /**
     * @param Element $element
     * @return mixed
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementItemGroupIdFieldValue(Element $element)
    {
        $result = null;
        $settings = $this->getSettings();

        if ($this->isUseProductId($settings->itemGroupIdField)) {
            if ($element instanceof Variant && $element->getProduct() != null) {
                $result = $element->getProduct()->getId();
            }
        } else {
            $result = $this->getElementFieldValue($element, $settings->itemGroupIdField);
        }

        return $result;
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @param mixed $customValue
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementCurrencyIso(Element $element, ?string $field, $customValue = null): ?string
    {
        if ($this->isUseBaseCurrency($field)) {
            $result = CommercePlugin::getInstance()->getPaymentCurrencies()->getPrimaryPaymentCurrencyIso();
        } else {
            $value = $this->getElementFieldValue($element, $field, $customValue);
            $result = is_array($value) ? current($value) : $value;
        }

        return $result;
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @param mixed $customValue
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementDimensionUnit(Element $element, ?string $field, $customValue = null): ?string
    {
        if ($this->isUseDimensionUnit($field)) {
            $result = CommercePlugin::getInstance()->getSettings()->dimensionUnits;
        } else {
            $value = $this->getElementFieldValue($element, $field, $customValue);
            $result = is_array($value) ? current($value) : $value;
        }

        return $result;
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @param mixed $customValue
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementWeightUnit(Element $element, ?string $field, $customValue = null): ?string
    {
        if ($this->isUseWeightUnit($field)) {
            $result = CommercePlugin::getInstance()->getSettings()->weightUnits;
        } else {
            $value = $this->getElementFieldValue($element, $field, $customValue);
            $result = is_array($value) ? current($value) : $value;
        }

        return $result;
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementSaleStartDate(Element $element, ?string $field): ?string
    {
        if ($this->isUseSaleStartDate($field)) {
            $value = $this->getElementSalesMinStartDate($element);
            $result = $value instanceof DateTime ? $value->format(DateTime::ATOM) : $value;
        } else {
            $value = $this->getElementFieldValue($element, $field);
            $result = is_array($value) ? current($value) : $value;
        }

        return $result;
    }

    /**
     * @param Element $element
     * @param string|null $field
     * @return string|null
     * @throws Exception
     * @since 1.3.0
     */
    public function getElementSaleEndDate(Element $element, ?string $field): ?string
    {
        if ($this->isUseSaleEndDate($field)) {
            $value = $this->getElementSalesMaxEndDate($element);
            $result = $value instanceof DateTime ? $value->format(DateTime::ATOM) : $value;
        } else {
            $value = $this->getElementFieldValue($element, $field);
            $result = is_array($value) ? current($value) : $value;
        }

        return $result;
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.3.0
     */
    public function isCustomValue(?string $value): bool
    {
        return $value == $this->getSettings()::OPTION_CUSTOM_VALUE;
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseProductId(?string $value): bool
    {
        return $value == $this->getSettings()::OPTION_USE_PRODUCT_ID;
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseSaleStartDate(?string $value): bool
    {
        return $value == $this->getSettings()::OPTION_USE_SALE_START_DATE;
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseSaleEndDate(?string $value): bool
    {
        return $value == $this->getSettings()::OPTION_USE_SALE_END_DATE;
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseStockField(?string $value): bool
    {
        return $value == null && $this->isCommerceInstalled();
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseBaseCurrency(?string $value): bool
    {
        return $value == null && $this->isCommerceInstalled();
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseDimensionUnit(?string $value): bool
    {
        return $value == null && $this->isCommerceInstalled();
    }

    /**
     * @param string|null $value
     * @return bool
     * @since 1.3.0
     */
    public function isUseWeightUnit(?string $value): bool
    {
        return $value == null && $this->isCommerceInstalled();
    }

    /**
     * @return bool
     * @since 1.4.0
     */
    public function isCommerceInstalled(): bool
    {
        return Craft::$app->getPlugins()->isPluginInstalled('commerce');
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return Settings
     * @since 1.4.0
     */
    protected function getSettings(): Settings
    {
        return GoogleShoppingFeedPro::$plugin->getSettings();
    }
}
