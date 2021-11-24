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



namespace Mirasvit\GeoIp\Service\Lookup;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Mirasvit\GeoIp\Model\ConfigProvider;

class IpProvider
{
    private $configProvider;

    private $remoteAddress;

    public function __construct(
        ConfigProvider $configProvider,
        RemoteAddress $remoteAddress
    ) {
        $this->configProvider = $configProvider;
        $this->remoteAddress  = $remoteAddress;
    }

    public function getIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }
}