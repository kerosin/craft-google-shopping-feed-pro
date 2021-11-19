<?php
/**
 * Google Shopping Feed Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\googleshoppingfeedpro\web\twig;

use Craft;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author    kerosin
 * @package   GoogleShoppingFeedPro
 * @since     1.3.1
 */
class Extension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getFilters(): array
    {
        if (!Craft::$app->getPlugins()->isPluginInstalled('commerce')) {
            return [
                new TwigFilter('commerceCurrency', function (
                    $amount,
                    $currency = null,
                    $convert = false,
                    $format = true,
                    $stripZeros = false
                ) {
                    return $amount;
                }),
            ];
        } else {
            return parent::getFilters();
        }
    }
}
