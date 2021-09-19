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
     */
    public function elementFieldValue(Element $element, string $field)
    {
        $settings = GoogleShoppingFeedPro::$plugin->getSettings();

        if (
            Craft::$app->getPlugins()->isPluginInstalled('commerce') &&
            $element instanceof Product &&
            in_array($field, array_keys($settings::getCommerceStandardFields()))
        ) {
            $object = $element->getDefaultVariant();
        } else {
            $object = $element;
        }

        return isset($object->$field) ? $object->$field : null;
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
     * @param Element[] $elements
     * @return void
     * @throws Exception
     */
    public function generateFeed(array $elements): void
    {
        set_time_limit(0);

        $response = Craft::$app->getResponse();
        $response->getHeaders()->set('Content-Type', 'application/xml; charset=UTF-8');

        /** @var GoogleShoppingFeedProService $googleShoppingFeedProService */
        $googleShoppingFeedProService = GoogleShoppingFeedPro::$plugin->googleShoppingFeedProService;

        echo $googleShoppingFeedProService->getFeedXml($elements);
    }
}
