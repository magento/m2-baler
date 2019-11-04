<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Baler\Model;

use Magento\Framework\View\Asset\File\FallbackContext as FileFallbackContext;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Asset\File;

/**
 * Class that creates needed assets for the page.
 */
class FileManager
{
    /**
     * @var AssetRepository
     */
    private $assetRepo;

    /**
     * @var FileFallbackContext
     */
    private $staticContext;

    /**
     * @param AssetRepository $assetRepo
     */
    public function __construct(AssetRepository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
        $this->staticContext = $assetRepo->getStaticViewFileContext();
    }

    /**
     * Create a view asset for core bundle JS config.
     *
     * @param string $bundleName
     * @return File
     */
    public function createCoreBundleJsAsset(string $bundleName): File
    {
        $relPath = $this->staticContext->getConfigPath() . '/balerbundles/' . $bundleName;
        return $this->assetRepo->createArbitrary($relPath, '');
    }

    /**
     * Create a view asset for require js config.
     *
     * @param string $fileName
     * @return File
     */
    public function createRequireJsConfigAsset(string $fileName): File
    {
        $relPath = $this->staticContext->getConfigPath() . '/' . $fileName;
        return $this->assetRepo->createArbitrary($relPath, '');
    }
}
