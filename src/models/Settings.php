<?php
/**
 * Google Shopping Feed Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\googleshoppingfeedpro\models;

use Craft;
use craft\base\Model;
use craft\commerce\Plugin as CommercePlugin;
use craft\helpers\ArrayHelper;

/**
 * @author    kerosin
 * @package   GoogleShoppingFeedPro
 * @since     1.0.0
 */
class Settings extends Model
{
    // Constants
    // =========================================================================

    const OPTION_CUSTOM_VALUE = '__custom_value__';
    /**
     * @since 1.1.0
     */
    const OPTION_USE_PRODUCT_ID = '__use_product_id__';
    /**
     * @since 1.2.0
     */
    const OPTION_USE_SALE_START_DATE = '__use_sale_start_date__';
    /**
     * @since 1.2.0
     */
    const OPTION_USE_SALE_END_DATE = '__use_sale_end_date__';

    const AVAILABILITY_IN_STOCK = 'in_stock';
    const AVAILABILITY_OUT_OF_STOCK = 'out_of_stock';

    /**
     * @since 1.4.0
     */
    const FILTER_STATUS_LIVE = 'live';
    /**
     * @since 1.4.0
     */
    const FILTER_STATUS_PENDING = 'pending';
    /**
     * @since 1.4.0
     */
    const FILTER_STATUS_EXPIRED = 'expired';
    /**
     * @since 1.4.0
     */
    const FILTER_STATUS_ENABLED = 'enabled';
    /**
     * @since 1.4.0
     */
    const FILTER_STATUS_DISABLED = 'disabled';
    /**
     * @since 1.4.0
     */
    const FILTER_STATUS_ARCHIVED = 'archived';

    // Public Properties
    // =========================================================================

    /**
     * Include variants.
     *
     * @var bool
     * @since 1.1.0
     */
    public $includeVariants = false;

    /**
     * Use product URL for variants.
     *
     * @var bool
     * @since 1.1.0
     */
    public $useProductUrl = true;

    /**
     * Use product data for variants.
     *
     * @var bool
     * @since 1.1.0
     */
    public $useProductData = true;

    /**
     * ID [id] field.
     *
     * @var string
     */
    public $idField;

    /**
     * Title [title] field.
     *
     * @var string
     */
    public $titleField;

    /**
     * Description [description] field.
     *
     * @var string
     */
    public $descriptionField;

    /**
     * Image link [image_link] field.
     *
     * @var string
     */
    public $imageLinkField;

    /**
     * Additional image link [additional_image_link] field.
     *
     * @var string
     */
    public $additionalImageLinkField;

    /**
     * Availability [availability] field.
     *
     * @var string
     */
    public $availabilityField;

    /**
     * Availability custom value.
     *
     * @var string
     */
    public $availabilityCustomValue;

    /**
     * Availability date [availability_date] field.
     *
     * @var string
     */
    public $availabilityDateField;

    /**
     * Cost of goods sold [cost_of_goods_sold] field.
     *
     * @var string
     */
    public $costOfGoodsSoldField;

    /**
     * Expiration date [expiration_date] field.
     *
     * @var string
     */
    public $expirationDateField;

    /**
     * Price [price] field.
     *
     * @var string
     */
    public $priceField;

    /**
     * Currency field.
     *
     * @var string
     */
    public $currencyField;

    /**
     * Currency custom value.
     *
     * @var string
     */
    public $currencyCustomValue;

    /**
     * Sale price [sale_price] field.
     *
     * @var string
     */
    public $salePriceField;

    /**
     * Sale price effective date [sale_price_effective_date] field.
     *
     * @var string
     */
    public $salePriceEffectiveDateFromField;

    /**
     * Sale price effective date [sale_price_effective_date] field.
     *
     * @var string
     */
    public $salePriceEffectiveDateToField;

    /**
     * Unit pricing measure [unit_pricing_measure] field.
     *
     * @var string
     */
    public $unitPricingMeasureField;

    /**
     * Unit pricing measure unit field.
     *
     * @var string
     */
    public $unitPricingMeasureUnitField;

    /**
     * Unit pricing measure unit custom value.
     *
     * @var string
     */
    public $unitPricingMeasureUnitCustomValue;

    /**
     * Unit pricing base measure [unit_pricing_base_measure] field.
     *
     * @var string
     */
    public $unitPricingBaseMeasureField;

    /**
     * Unit pricing base measure unit field.
     *
     * @var string
     */
    public $unitPricingBaseMeasureUnitField;

    /**
     * Unit pricing base measure unit custom value.
     *
     * @var string
     */
    public $unitPricingBaseMeasureUnitCustomValue;

    /**
     * Installment [installment] field.
     *
     * @var string
     */
    public $installmentField;

    /**
     * Subscription cost [subscription_cost] field.
     *
     * @var string
     */
    public $subscriptionCostField;

    /**
     * Loyalty points [loyalty_points] field.
     *
     * @var string
     */
    public $loyaltyPointsField;

    /**
     * Google product category [google_product_category] field.
     *
     * @var string
     */
    public $googleProductCategoryField;

    /**
     * Google product category custom value.
     *
     * @var string
     */
    public $googleProductCategoryCustomValue;

    /**
     * Product type [product_type] field.
     *
     * @var string
     */
    public $productTypeField;

    /**
     * Brand [brand] field.
     *
     * @var string
     */
    public $brandField;

    /**
     * Brand custom value.
     *
     * @var string
     */
    public $brandCustomValue;

    /**
     * GTIN [gtin] field.
     *
     * @var string
     */
    public $gtinField;

    /**
     * MPN [mpn] field.
     *
     * @var string
     */
    public $mpnField;

    /**
     * Identifier exists [identifier_exists] field.
     *
     * @var string
     */
    public $identifierExistsField;

    /**
     * Condition [condition] field.
     *
     * @var string
     */
    public $conditionField;

    /**
     * Condition custom value.
     *
     * @var string
     */
    public $conditionCustomValue;

    /**
     * Adult [adult] field.
     *
     * @var string
     */
    public $adultField;

    /**
     * Adult custom value.
     *
     * @var string
     */
    public $adultCustomValue;

    /**
     * Multipack [multipack] field.
     *
     * @var string
     */
    public $multipackField;

    /**
     * Bundle [is_bundle] field.
     *
     * @var string
     */
    public $isBundleField;

    /**
     * Energy efficiency class [energy_efficiency_class] field.
     *
     * @var string
     */
    public $energyEfficiencyClassField;

    /**
     * Energy efficiency class custom value.
     *
     * @var string
     */
    public $energyEfficiencyClassCustomValue;

    /**
     * Minimum energy efficiency class [min_energy_efficiency_class] field.
     *
     * @var string
     */
    public $minEnergyEfficiencyClassField;

    /**
     * Minimum energy efficiency class custom value.
     *
     * @var string
     */
    public $minEnergyEfficiencyClassCustomValue;

    /**
     * Maximum energy efficiency class [max_energy_efficiency_class] field.
     *
     * @var string
     */
    public $maxEnergyEfficiencyClassField;

    /**
     * Maximum energy efficiency class custom value.
     *
     * @var string
     */
    public $maxEnergyEfficiencyClassCustomValue;

    /**
     * Age group [age_group] field.
     *
     * @var string
     */
    public $ageGroupField;

    /**
     * Age group custom value.
     *
     * @var string
     */
    public $ageGroupCustomValue;

    /**
     * Color [color] field.
     *
     * @var string
     */
    public $colorField;

    /**
     * Color custom value.
     *
     * @var string
     */
    public $colorCustomValue;

    /**
     * Gender [gender] field.
     *
     * @var string
     */
    public $genderField;

    /**
     * Gender custom value.
     *
     * @var string
     */
    public $genderCustomValue;

    /**
     * Material [material] field.
     *
     * @var string
     */
    public $materialField;

    /**
     * Material custom value.
     *
     * @var string
     */
    public $materialCustomValue;

    /**
     * Pattern [pattern] field.
     *
     * @var string
     */
    public $patternField;

    /**
     * Pattern custom value.
     *
     * @var string
     */
    public $patternCustomValue;

    /**
     * Size [size] field.
     *
     * @var string
     */
    public $sizeField;

    /**
     * Size custom value.
     *
     * @var string
     */
    public $sizeCustomValue;

    /**
     * Size type [size_type] field.
     *
     * @var string
     */
    public $sizeTypeField;

    /**
     * Size type custom value.
     *
     * @var string
     */
    public $sizeTypeCustomValue;

    /**
     * Size system [size_system] field.
     *
     * @var string
     */
    public $sizeSystemField;

    /**
     * Size system custom value.
     *
     * @var string
     */
    public $sizeSystemCustomValue;

    /**
     * Item group ID [item_group_id] field.
     *
     * @var string
     */
    public $itemGroupIdField;

    /**
     * Product length [product_length] field.
     *
     * @var string
     */
    public $productLengthField;

    /**
     * Product length unit field.
     *
     * @var string
     */
    public $productLengthUnitField;

    /**
     * Product length unit custom value.
     *
     * @var string
     */
    public $productLengthUnitCustomValue;

    /**
     * Product width [product_width] field.
     *
     * @var string
     */
    public $productWidthField;

    /**
     * Product width unit field.
     *
     * @var string
     */
    public $productWidthUnitField;

    /**
     * Product width unit custom value.
     *
     * @var string
     */
    public $productWidthUnitCustomValue;

    /**
     * Product height [product_height] field.
     *
     * @var string
     */
    public $productHeightField;

    /**
     * Product height unit field.
     *
     * @var string
     */
    public $productHeightUnitField;

    /**
     * Product height unit custom value.
     *
     * @var string
     */
    public $productHeightUnitCustomValue;

    /**
     * Product weight [product_weight] field.
     *
     * @var string
     */
    public $productWeightField;

    /**
     * Product weight unit field.
     *
     * @var string
     */
    public $productWeightUnitField;

    /**
     * Product weight unit custom value.
     *
     * @var string
     */
    public $productWeightUnitCustomValue;

    /**
     * Product detail [product_detail] field.
     *
     * @var string
     */
    public $productDetailField;

    /**
     * Product highlight [product_highlight] field.
     *
     * @var string
     */
    public $productHighlightField;

    /**
     * Entry status filter.
     *
     * @var array
     * @since 1.4.0
     */
    public $entryStatusFilter = [self::FILTER_STATUS_LIVE];

    /**
     * Entry type filter.
     *
     * @var array
     * @since 1.4.0
     */
    public $entryTypeFilter = [];

    /**
     * Entry category filter.
     *
     * @var array
     * @since 1.4.0
     */
    public $entryCategoryFilter = [];

    /**
     * Product status filter.
     *
     * @var array
     * @since 1.4.0
     */
    public $productStatusFilter = [self::FILTER_STATUS_LIVE];

    /**
     * Product type filter.
     *
     * @var array
     * @since 1.4.0
     */
    public $productTypeFilter = [];

    /**
     * Product category filter.
     *
     * @var array
     * @since 1.4.0
     */
    public $productCategoryFilter = [];

    /**
     * Product available for purchase filter.
     *
     * @var string
     * @since 1.4.0
     */
    public $productAvailableForPurchaseFilter;

    // Public Methods
    // =========================================================================

    /**
     * @return array
     */
    public static function getCmsStandardFields(): array
    {
        return [
            'id' => Craft::t('google-shopping-feed-pro', 'ID'),
            'title' => Craft::t('google-shopping-feed-pro', 'Title'),
            'expiryDate' => Craft::t('google-shopping-feed-pro', 'Expiry Date'),
        ];
    }

    /**
     * @return array
     */
    public static function getCommerceStandardFields(): array
    {
        return [
            'sku' => Craft::t('google-shopping-feed-pro', 'SKU'),
            'price' => Craft::t('google-shopping-feed-pro', 'Price'),
            'salePrice' => Craft::t('google-shopping-feed-pro', 'Sale Price'),
            'length' => Craft::t('google-shopping-feed-pro', 'Dimensions (L)'),
            'width' => Craft::t('google-shopping-feed-pro', 'Dimensions (W)'),
            'height' => Craft::t('google-shopping-feed-pro', 'Dimensions (H)'),
            'weight' => Craft::t('google-shopping-feed-pro', 'Weight'),
        ];
    }

    /**
     * @return array
     */
    public function getStandardFields(): array
    {
        $result = self::getCmsStandardFields();

        if (Craft::$app->getPlugins()->isPluginInstalled('commerce')) {
            $result = array_merge($result, self::getCommerceStandardFields());
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getCustomFields(): array
    {
        $result = [];

        foreach (Craft::$app->getFields()->getAllFields() as $field) {
            $result[$field->handle] = $field->name;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getFieldOptions(): array
    {
        $result = [];
        $fields = $this->getStandardFields();

        if (count($fields)) {
            $result[] = ['optgroup' => Craft::t('google-shopping-feed-pro', 'Standard Fields')];

            foreach ($fields as $handle => $name) {
                $result[] = [
                    'value' => $handle,
                    'label' => $name,
                ];
            }
        }

        $fields = $this->getCustomFields();

        if (count($fields)) {
            $result[] = ['optgroup' => Craft::t('google-shopping-feed-pro', 'Custom Fields')];

            foreach ($fields as $handle => $name) {
                $result[] = [
                    'value' => $handle,
                    'label' => $name,
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     * @since 1.4.0
     */
    public function getStatusFilterOptions(): array
    {
        return [
            self::FILTER_STATUS_LIVE => Craft::t('google-shopping-feed-pro', 'Live'),
            self::FILTER_STATUS_PENDING => Craft::t('google-shopping-feed-pro', 'Pending'),
            self::FILTER_STATUS_EXPIRED => Craft::t('google-shopping-feed-pro', 'Expired'),
            self::FILTER_STATUS_ENABLED => Craft::t('google-shopping-feed-pro', 'Enabled'),
            self::FILTER_STATUS_DISABLED => Craft::t('google-shopping-feed-pro', 'Disabled'),
            self::FILTER_STATUS_ARCHIVED => Craft::t('google-shopping-feed-pro', 'Archived'),
        ];
    }

    /**
     * @return array
     * @since 1.4.0
     */
    public function getEntryTypeFilterOptions(): array
    {
        $result = [];
        $sections = Craft::$app->getSections()->getAllSections();

        foreach ($sections as $section) {
            foreach ($section->getEntryTypes() as $entryType) {
                $result[] = [
                    'value' => $entryType->id,
                    'label' => Craft::t('site', $section->name) . ' - ' . Craft::t('site', $entryType->name),
                ];
            }
        }

        return $result;
    }

    /**
     * @return array
     * @since 1.4.0
     */
    public function getProductTypeFilterOptions(): array
    {
        $result = [];

        if (!Craft::$app->getPlugins()->isPluginInstalled('commerce')) {
            return $result;
        }

        $productTypes = CommercePlugin::getInstance()->getProductTypes()->getAllProductTypes();

        foreach ($productTypes as $productType) {
            $result[] = [
                'value' => $productType->id,
                'label' => Craft::t('site', $productType->name),
            ];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $fieldOptions = array_merge(
            [
                self::OPTION_CUSTOM_VALUE,
                self::OPTION_USE_PRODUCT_ID,
                self::OPTION_USE_SALE_START_DATE,
                self::OPTION_USE_SALE_END_DATE,
            ],
            array_keys($this->getStandardFields()),
            array_keys($this->getCustomFields())
        );
        $statusFilterOptions = array_keys($this->getStatusFilterOptions());
        $entryTypeFilterOptions = ArrayHelper::getColumn($this->getEntryTypeFilterOptions(), 'value');
        $productTypeFilterOptions = ArrayHelper::getColumn($this->getProductTypeFilterOptions(), 'value');

        return [
            ['includeVariants', 'boolean'],
            ['useProductUrl', 'boolean'],
            ['useProductData', 'boolean'],
            ['idField', 'in', 'range' => $fieldOptions],
            ['titleField', 'in', 'range' => $fieldOptions],
            ['descriptionField', 'in', 'range' => $fieldOptions],
            ['imageLinkField', 'in', 'range' => $fieldOptions],
            ['additionalImageLinkField', 'in', 'range' => $fieldOptions],
            ['availabilityField', 'in', 'range' => $fieldOptions],
            ['availabilityDateField', 'in', 'range' => $fieldOptions],
            ['costOfGoodsSoldField', 'in', 'range' => $fieldOptions],
            ['expirationDateField', 'in', 'range' => $fieldOptions],
            ['priceField', 'in', 'range' => $fieldOptions],
            ['currencyField', 'in', 'range' => $fieldOptions],
            ['salePriceField', 'in', 'range' => $fieldOptions],
            ['salePriceEffectiveDateFromField', 'in', 'range' => $fieldOptions],
            ['salePriceEffectiveDateToField', 'in', 'range' => $fieldOptions],
            ['unitPricingMeasureField', 'in', 'range' => $fieldOptions],
            ['unitPricingMeasureUnitField', 'in', 'range' => $fieldOptions],
            ['unitPricingBaseMeasureField', 'in', 'range' => $fieldOptions],
            ['unitPricingBaseMeasureUnitField', 'in', 'range' => $fieldOptions],
            ['installmentField', 'in', 'range' => $fieldOptions],
            ['subscriptionCostField', 'in', 'range' => $fieldOptions],
            ['loyaltyPointsField', 'in', 'range' => $fieldOptions],
            ['googleProductCategoryField', 'in', 'range' => $fieldOptions],
            ['productTypeField', 'in', 'range' => $fieldOptions],
            ['brandField', 'in', 'range' => $fieldOptions],
            ['gtinField', 'in', 'range' => $fieldOptions],
            ['mpnField', 'in', 'range' => $fieldOptions],
            ['identifierExistsField', 'in', 'range' => $fieldOptions],
            ['conditionField', 'in', 'range' => $fieldOptions],
            ['adultField', 'in', 'range' => $fieldOptions],
            ['multipackField', 'in', 'range' => $fieldOptions],
            ['isBundleField', 'in', 'range' => $fieldOptions],
            ['energyEfficiencyClassField', 'in', 'range' => $fieldOptions],
            ['minEnergyEfficiencyClassField', 'in', 'range' => $fieldOptions],
            ['maxEnergyEfficiencyClassField', 'in', 'range' => $fieldOptions],
            ['ageGroupField', 'in', 'range' => $fieldOptions],
            ['colorField', 'in', 'range' => $fieldOptions],
            ['genderField', 'in', 'range' => $fieldOptions],
            ['materialField', 'in', 'range' => $fieldOptions],
            ['patternField', 'in', 'range' => $fieldOptions],
            ['sizeField', 'in', 'range' => $fieldOptions],
            ['sizeTypeField', 'in', 'range' => $fieldOptions],
            ['sizeSystemField', 'in', 'range' => $fieldOptions],
            ['itemGroupIdField', 'in', 'range' => $fieldOptions],
            ['productLengthField', 'in', 'range' => $fieldOptions],
            ['productLengthUnitField', 'in', 'range' => $fieldOptions],
            ['productWidthField', 'in', 'range' => $fieldOptions],
            ['productWidthUnitField', 'in', 'range' => $fieldOptions],
            ['productHeightField', 'in', 'range' => $fieldOptions],
            ['productHeightUnitField', 'in', 'range' => $fieldOptions],
            ['productWeightField', 'in', 'range' => $fieldOptions],
            ['productWeightUnitField', 'in', 'range' => $fieldOptions],
            ['productDetailField', 'in', 'range' => $fieldOptions],
            ['productHighlightField', 'in', 'range' => $fieldOptions],
            ['entryStatusFilter', 'in', 'allowArray' => true, 'range' => $statusFilterOptions],
            ['entryTypeFilter', 'in', 'allowArray' => true, 'range' => $entryTypeFilterOptions],
            ['productStatusFilter', 'in', 'allowArray' => true, 'range' => $statusFilterOptions],
            ['productTypeFilter', 'in', 'allowArray' => true, 'range' => $productTypeFilterOptions],
        ];
    }
}
