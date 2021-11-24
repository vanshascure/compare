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



namespace Mirasvit\GeoIp\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    const LOOKUP_SOURCE_GEO_LITE = 'geoLite';
    const LOOKUP_SOURCE_IP_API   = 'ip_api';

    const POPUP_TYPE_NONE         = 'none';
    const POPUP_TYPE_NOTIFICATION = 'notification';
    const POPUP_TYPE_CONFIRMATION = 'confirmation';

    const RULE_SELECTOR_REDIRECT = 'redirect';
    const RULE_SELECTOR_CURRENCY = 'currency';
    const RULE_SELECTOR_RESTRICT = 'restrict';
    const RULE_SELECTOR_STORE    = 'store';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue('geo_ip/general/is_enabled');
    }

    public function getCountryLookupSource()
    {
        return $this->scopeConfig->getValue('geo_ip/lookup/source') === self::LOOKUP_SOURCE_IP_API
            ? self::LOOKUP_SOURCE_IP_API
            : self::LOOKUP_SOURCE_GEO_LITE;
    }

    public function isDebugMode()
    {
        return $this->scopeConfig->getValue('geo_ip/lookup/is_debug');
    }

    public function getDebugIp()
    {
        return trim($this->scopeConfig->getValue('geo_ip/lookup/debug_ip'));
    }

    public function getPopupType()
    {
        $type = $this->scopeConfig->getValue('geo_ip/general/popup_type');

        return $type ? $type : self::POPUP_TYPE_NONE;
    }

    public function getPopupText()
    {
        return $this->scopeConfig->getValue('geo_ip/general/popup_text');
    }

    public function getLimitationIgnoredIps()
    {
        return explode(',', $this->scopeConfig->getValue('geo_ip/limitations/ignored_ip'));
    }

    public function getLimitationAgents()
    {
        return explode(',', $this->scopeConfig->getValue('geo_ip/limitations/ignored_agent'));
    }

    public function isProcessOnFirstVisit()
    {
        return $this->scopeConfig->getValue('geo_ip/limitations/process_first_visit');
    }

    public function isAjaxMode()
    {
        return (bool)$this->scopeConfig->getValue('geo_ip/general/ajax_mode');
    }
}
