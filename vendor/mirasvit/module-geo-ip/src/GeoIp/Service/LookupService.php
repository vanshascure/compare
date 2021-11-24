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

use Magento\Framework\App\RequestInterface;
use Mirasvit\GeoIp\Model\ConfigProvider;

class LookupService
{
    private $configProvider;

    private $countryProvider;

    private $ipProvider;

    private $localeProvider;

    private $request;

    public function __construct(
        ConfigProvider $configProvider,
        RequestInterface $request,
        Lookup\CountryProvider $countryProvider,
        Lookup\IpProvider $ipProvider,
        Lookup\LocaleProvider $localeProvider
    ) {
        $this->configProvider  = $configProvider;
        $this->request         = $request;
        $this->countryProvider = $countryProvider;
        $this->ipProvider      = $ipProvider;
        $this->localeProvider  = $localeProvider;
    }

    public function getCountry()
    {
        return $this->countryProvider->getCountry($this->getIp());
    }

    public function getLocale()
    {
        return $this->localeProvider->getLocale();
    }

    public function getIp()
    {
        if ($this->configProvider->isDebugMode() && $this->configProvider->getDebugIp()) {
            return $this->configProvider->getDebugIp();
        }

        return $this->ipProvider->getIp();
    }

    public function getUserAgent()
    {
        return strtolower($this->request->getHeader('USER_AGENT'));
    }
}