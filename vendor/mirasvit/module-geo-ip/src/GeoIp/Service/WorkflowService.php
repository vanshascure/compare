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
use Magento\Framework\Session\SessionManagerInterface;
use Mirasvit\GeoIp\Model\ConfigProvider;

class WorkflowService
{
    const FLAG_REQUEST        = 'request';
    const FLAG_REQUEST_STATUS = 'request_status';

    const FLAG_LOCATION = 'location';
    const FLAG_CURRENCY = 'currency';

    private $configProvider;

    private $sessionManager;

    private $request;

    public function __construct(
        ConfigProvider $configProvider,
        SessionManagerInterface $sessionManager,
        RequestInterface $request
    ) {
        $this->configProvider = $configProvider;
        $this->sessionManager = $sessionManager;
        $this->request        = $request;
    }

    public function canLocationChange()
    {
        if ($this->configProvider->isProcessOnFirstVisit() && $this->isLocationChanged()) {
            return false;
        }

        if ($this->configProvider->getPopupType() !== ConfigProvider::POPUP_TYPE_NONE) {
            return $this->isRequestApproved();
        }

        return true;
    }

    public function requestLocationChange()
    {
        if ($this->isRequestApproved() || $this->isRequestRejected()) {
            return;
        }

        $this->setValue(self::FLAG_REQUEST, true);

        // prevent to use FPC version
        $this->request->setParam('X-Magento-Vary', microtime(true));
    }

    public function submitLocationChange()
    {
        $this->setValue(self::FLAG_LOCATION, true);
    }

    public function isLocationChanged()
    {
        return $this->getValue(self::FLAG_LOCATION);
    }

    public function canCurrencyChange()
    {
        if ($this->configProvider->isProcessOnFirstVisit() && $this->isCurrencyChanged()) {
            return false;
        }

        return true;
    }

    public function submitCurrencyChange()
    {
        $this->setValue(self::FLAG_CURRENCY, true);
    }

    public function isCurrencyChanged()
    {
        return $this->getValue(self::FLAG_CURRENCY);
    }

    public function isShowRequest()
    {
        //return true; // uncomment for test
        return $this->getValue(self::FLAG_REQUEST);
    }

    public function isRequestApproved()
    {
        return $this->getValue(self::FLAG_REQUEST_STATUS) === 'accept';
    }

    public function isRequestRejected()
    {
        return $this->getValue(self::FLAG_REQUEST_STATUS) === 'reject';
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setRequestStatus($status)
    {
        $this->setValue(self::FLAG_REQUEST, false);

        return $this->setValue(self::FLAG_REQUEST_STATUS, $status);
    }

    /**
     * @param string      $name
     * @param bool|string $value
     *
     * @return $this
     */
    private function setValue($name, $value)
    {
        $this->sessionManager->setData('geo_ip_' . $name, $value);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getValue($name)
    {
        return $this->sessionManager->getData('geo_ip_' . $name);
    }
}
