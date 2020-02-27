<?php

namespace App\Service;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class SmartAssetsHelperService.
 *
 * @category Service
 */
class SmartAssetsHelperService
{
    const HTTP_PROTOCOL = 'https://';
    const PHP_SERVER_API_CLI_CONTEXT = 'cli';

    /**
     * @var UploaderHelper
     */
    private UploaderHelper $uploaderHelper;

    /**
     * @var CacheManager
     */
    private CacheManager $cacheManager;

    /**
     * @var DataManager
     */
    private DataManager $dataManager;

    /**
     * @var FilterManager
     */
    private FilterManager $filterManager;

    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    /**
     * @var string mailer URL base
     */
    private string $mub;

    /**
     * Methods.
     */

    /**
     * SmartAssetsHelperService constructor.
     *
     * @param UploaderHelper  $uploaderHelper
     * @param CacheManager    $cacheManager
     * @param DataManager     $dataManager
     * @param FilterManager   $filterManager
     * @param KernelInterface $kernel
     * @param string          $mub
     */
    public function __construct(UploaderHelper $uploaderHelper, CacheManager $cacheManager, DataManager $dataManager, FilterManager $filterManager, KernelInterface $kernel, $mub)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->cacheManager = $cacheManager;
        $this->dataManager = $dataManager;
        $this->filterManager = $filterManager;
        $this->kernel = $kernel;
        $this->mub = $mub;
    }

    /**
     * Determine if this PHP script is executed under a CLI context.
     *
     * @return bool
     */
    public function isCliContext()
    {
        return self::PHP_SERVER_API_CLI_CONTEXT === php_sapi_name();
    }

    /**
     * Always return absolute URL path, even in CLI contexts.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getAbsoluteAssetPathContextIndependent($assetPath)
    {
        $package = new UrlPackage(self::HTTP_PROTOCOL.$this->mub.'/', new EmptyVersionStrategy());

        return $package->getUrl($assetPath);
    }

    /**
     * Always return absolute URL path, even in CLI contexts.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getAbsoluteAssetPathContextIndependentWithVersionStrategy($assetPath)
    {
        $package = new UrlPackage(
            'file:/'.$this->kernel->getProjectDir().'/public',
            new JsonManifestVersionStrategy($this->kernel->getProjectDir().'/public/build/manifest.json')
        );

        return substr($package->getUrl($assetPath), 6);
    }

    /**
     * If is CLI context returns absolute file path, otherwise returns absolute URL path.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getAbsoluteAssetPathByContext($assetPath)
    {
        $result = $this->getAbsoluteAssetPathContextIndependent($assetPath);

        if ($this->isCliContext()) {
            $result = $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.$assetPath;
        }

        return $result;
    }

    /**
     * Always return relative URL path, even in CLI contexts.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getRelativeAssetPathContextIndependent($assetPath)
    {
        $package = new UrlPackage('/', new EmptyVersionStrategy());

        return $package->getUrl($assetPath);
    }

    /**
     * If is CLI context returns absolute file path, otherwise returns relative URL path.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getRelativeAssetPathByContext($assetPath)
    {
        $result = $this->getRelativeAssetPathContextIndependent($assetPath);

        if ($this->isCliContext()) {
            $result = $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.$assetPath;
        }

        return $result;
    }

    /**
     * Returns absolute file path.
     *
     * @param string $assetPath
     *
     * @return string
     */
    public function getAbsoluteAssetFilePath($assetPath)
    {
        return $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'public'.$assetPath;
    }

    /**
     * Returns absolute file path and build a previous image cache if it is missing.
     *
     * @param string $assetPath
     * @param string $liipImagineFilter
     *
     * @return string
     */
    public function getAbsoluteLiipMediaCacheAssetFilePathByFilterAndResolveItIfIsNecessary($assetPath, $liipImagineFilter)
    {
        if (!$this->cacheManager->isStored($assetPath, $liipImagineFilter)) {
            $this->cacheManager->store($this->filterManager->applyFilter($this->dataManager->find($liipImagineFilter, $assetPath), $liipImagineFilter), $assetPath, $liipImagineFilter);
        }

        $url = $this->cacheManager->generateUrl($assetPath, $liipImagineFilter, [], null, UrlGeneratorInterface::ABSOLUTE_PATH);
        $url = str_replace('/resolve/', '/', $url);

        return $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.'public'.$url;
    }

    /**
     * @param object|mixed $object
     * @param string       $mapping Vich attribute name
     * @param string       $filter  Liip filter name
     *
     * @return string
     */
    public function getPublicPathForLiipFilter($object, $mapping, $filter)
    {
        return $this->cacheManager->getBrowserPath($this->uploaderHelper->asset($object, $mapping), $filter);
    }
}
