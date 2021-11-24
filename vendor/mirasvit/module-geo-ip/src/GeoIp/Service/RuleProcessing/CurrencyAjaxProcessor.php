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



namespace Mirasvit\GeoIp\Service\RuleProcessing;

use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\GeoIp\Service\WorkflowService;
use Mirasvit\GeoIp\Model\ConfigProvider;
use Magento\Framework\App\RequestInterface;

class CurrencyAjaxProcessor
{
    private $workflowService;

    private $storeManager;

    private $configProvider;

    private $request;

    public function __construct(
        ConfigProvider $configProvider,
        WorkflowService $workflowService,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->configProvider  = $configProvider;
        $this->workflowService = $workflowService;
        $this->request         = $request;
        $this->storeManager    = $storeManager;
    }

    /**
     * @param string $currencyCode
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function process($currencyCode)
    {
        $relativePageUrl = $this->request->getPostValue('relativePageUrl');

        if (!$currencyCode) {
            return [];
        }

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();
        if ($store->getCurrentCurrencyCode() === $currencyCode) {
            return [];
        }

        if (!$this->workflowService->canCurrencyChange()) {
            return [];
        }

        $store->setCurrentCurrencyCode($currencyCode);

        return [
            'ruleSelector'          => ConfigProvider::RULE_SELECTOR_CURRENCY,
            'popupType'             => $this->configProvider->getPopupType(),
            'redirectUrl'           => $relativePageUrl,
            'isLocationChanged'     => (bool)$this->workflowService->isLocationChanged(),
            'isProcessOnFirstVisit' => (bool)$this->configProvider->isProcessOnFirstVisit(),
            'isRequestApproved'     => (bool)$this->workflowService->isRequestApproved(),
            'isRequestRejected'     => (bool)$this->workflowService->isRequestRejected(),
        ];
    }

}
