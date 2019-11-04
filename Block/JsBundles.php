<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Baler\Block;

use Magento\Baler\Model\FileManager;
use Magento\Framework\View\Element\Context;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\RequireJs\Config as RequireJsConfig;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Block needed to handle JS bundles in layout.
 */
class JsBundles extends AbstractBlock
{
    /**
     * @var string
     */
    private static $requireJsBundleConfig = 'requirejs-bundle-config.js';

    /**
     * @var string
     */
    private static $requireJsConfigFile = 'requirejs-config.js';

    /**
     * @var string
     */
    private static $coreBundle = 'core-bundle.js';

    /**
     * @var DirectoryList
     */
    private $dir;

    /**
     * @var FileManager
     */
    private $fileManager;

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @var RequireJsConfig
     */
    private $requireJsConfig;

    /**
     * @param Context $context
     * @param DirectoryList $dir
     * @param FileManager $fileManager
     * @param PageConfig $pageConfig
     * @param RequireJsConfig $requireJsConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        DirectoryList $dir,
        FileManager $fileManager,
        PageConfig $pageConfig,
        RequireJsConfig $requireJsConfig,
        array $data = []
    ) {
        $this->dir = $dir;
        $this->fileManager = $fileManager;
        $this->pageConfig = $pageConfig;
        $this->requireJsConfig = $requireJsConfig;
        parent::__construct($context, $data);
    }

    /**
     * Preparing layout to use JS bundles.
     *
     * @return AbstractBlock
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function _prepareLayout(): AbstractBlock
    {
        $staticDir = $this->dir->getPath('static');
        $assetCollection = $this->pageConfig->getAssetCollection();
        // replace requirejs-config.js in the head of the document with requirejs-bundle-config.js
        $requireJsBundleConfig = $this->fileManager->createRequireJsConfigAsset(self::$requireJsBundleConfig);
        $requireJsBalerConfigRelPath = $requireJsBundleConfig->getFilePath();
        $requireJsBalerConfigAbsPath = $staticDir . '/' . $requireJsBalerConfigRelPath;
        if (file_exists($requireJsBalerConfigAbsPath)) {
            $assetCollection->insert(
                $requireJsBalerConfigRelPath,
                $requireJsBundleConfig,
                $this->requireJsConfig->getMixinsFileRelativePath()
            );
            $requireJsConfig = $this->fileManager->createRequireJsConfigAsset(self::$requireJsConfigFile);
            $requireJsConfigRelPath = $requireJsConfig->getFilePath();
            $assetCollection->remove($requireJsConfigRelPath);
        }
        // add core-bundle.js file
        $coreBundleJsConfig = $this->fileManager->createCoreBundleJsAsset(self::$coreBundle);
        $coreBundleJsConfigRelPath = $coreBundleJsConfig->getFilePath();
        $coreBundleJsConfigAbsPath = $staticDir . '/' . $coreBundleJsConfigRelPath;
        if (file_exists($coreBundleJsConfigAbsPath)) {
            $assetCollection->insert(
                $coreBundleJsConfigAbsPath,
                $coreBundleJsConfig,
                $this->requireJsConfig->getMixinsFileRelativePath()
            );
        }

        return parent::_prepareLayout();
    }
}
