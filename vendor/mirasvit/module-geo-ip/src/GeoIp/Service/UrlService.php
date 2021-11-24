<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-geo-ip
 * @version   1.1.2
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\GeoIp\Service;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class UrlService
{

    private $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $urlA
     * @param string $urlB
     *
     * @return bool
     */
    public function isEquivalentUrl($urlA, $urlB)
    {
        $urlPartsA = parse_url($urlA);
        $urlPartsB = parse_url($urlB);

        if (!is_array($urlPartsA) || !is_array($urlPartsB)) {
            return false;
        }

        $pathA = '';
        if (isset($urlPartsA['path'])) {
            $pathA = $urlPartsA['path'];
        }

        $pathB = '';
        if (isset($urlPartsB['path'])) {
            $pathB = $urlPartsB['path'];
        }

        $queryA = '';
        if (isset($urlPartsA['query'])) {
            $queryA = $urlPartsA['query'];
        }

        $queryB = '';
        if (isset($urlPartsB['query'])) {
            $queryB = $urlPartsB['query'];
        }

        if ($pathA == $pathB && $queryA == $queryB) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRedirectUrls()
    {
        $urls          = [];
        $redirectRoute = 'geo_ip/action/redirect';
        /** @var Store $store */
        foreach ($this->storeManager->getStores() as $store) {
            $urls[$store->getId()] = $store->getUrl($redirectRoute);
        }

        return $urls;
    }
}
