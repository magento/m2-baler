<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Baler\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Response\Http;

/**
 * Plugin for asynchronous core JS bundle loading.
 */
class AsyncCoreBundleLoadPlugin
{
    private const XML_PATH_USE_JS_BALER_BUNDLING = 'dev/js/enable_baler_js_bundling';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Load core JS bundle asynchronously if it is enabled in configuration.
     *
     * @param Http $subject
     * @return void
     */
    public function beforeSendResponse(Http $subject): void
    {
        $content = $subject->getContent();

        if (is_string($content) && strpos($content, '</body') !== false && $this->scopeConfig->isSetFlag(
                self::XML_PATH_USE_JS_BALER_BUNDLING,
                ScopeInterface::SCOPE_STORE
            )) {
            $content = preg_replace_callback(
                '@<script\b.*?src=("|\')(.+?/balerbundles/core-bundle\.js)\1.*?script>@',
                function ($matches) {
                    $href = $matches[2];
                    return sprintf('<link rel="preload" as="script" href="%s">', $href);
                },
                $content
            );

            $subject->setContent($content);
        }
    }
}
