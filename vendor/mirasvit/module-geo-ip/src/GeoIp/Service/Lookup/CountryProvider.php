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

use GeoIp2\Database\Reader as GeoIp2Reader;
use Mirasvit\GeoIp\Model\ConfigProvider;

class CountryProvider
{
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    /**
     * @param string $ip
     *
     * @return string
     */
    public function getCountry($ip)
    {
        if ($this->configProvider->getCountryLookupSource() == ConfigProvider::LOOKUP_SOURCE_GEO_LITE) {
            $country = $this->lookupInDb($ip);
        } else {
            $country = $this->lookupInApi($ip);
        }

        return $country;
    }

    /**
     * @param string $ip
     *
     * @return string
     */
    private function lookupInDb($ip)
    {
        $reader = new GeoIp2Reader(dirname(__FILE__, 3) . '/Setup/GeoLite2-City.mmdb');

        try {
            $data = $reader->city($ip);
            if ($data) {
                return $data->country->isoCode;
            }
        } catch (\Exception $e) {
        }

        return '';
    }

    /**
     * @param string $ip
     *
     * @return string
     */
    private function lookupInApi($ip)
    {
        try {
            $data = \Zend_Json::decode(file_get_contents('http://ip-api.com/json/' . $ip));
            if (isset($data['countryCode'])) {
                return $data['countryCode'];
            }
        } catch (\Exception $e) {
        }

        return '';
    }
}