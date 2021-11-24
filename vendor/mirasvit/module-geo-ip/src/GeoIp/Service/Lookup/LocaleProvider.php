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

use Magento\Framework\HTTP\Header as HttpHeader;

class LocaleProvider
{
    private $httpHeader;

    public function __construct(
        HttpHeader $httpHeader
    ) {
        $this->httpHeader = $httpHeader;
    }

    public function getLocale()
    {
        $crumbs = explode(',', $this->httpHeader->getHttpAcceptLanguage());
        $locale = $crumbs[0];
        $locale = str_replace('_', '-', $locale);
        $parts  = explode('-', $locale);

        if (!isset($parts[0])) {
            return 'en_US';
        }
        $langCode = strtolower($parts[0]);

        $countryCode = '*';
        if (isset($parts[1])) {
            $countryCode = strtoupper($parts[1]);
        }

        return $langCode . '_' . $countryCode;
    }

}
