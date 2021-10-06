<?php
/**
 * Google Shopping Feed Pro plugin for Craft CMS 3.x
 *
 * @link      https://github.com/kerosin
 * @copyright Copyright (c) 2021 kerosin
 */

namespace kerosin\googleshoppingfeedpro\assetbundles\googleshoppingfeedpro;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    kerosin
 * @package   GoogleShoppingFeedPro
 * @since     1.2.2
 */
class GoogleShoppingFeedProAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        $this->sourcePath = '@kerosin/googleshoppingfeedpro/assetbundles/googleshoppingfeedpro/dist';

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/app.css',
        ];

        parent::init();
    }
}
