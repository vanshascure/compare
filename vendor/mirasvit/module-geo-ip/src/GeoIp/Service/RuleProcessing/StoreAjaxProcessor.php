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

use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\GeoIp\Service\WorkflowService;
use Mirasvit\GeoIp\Model\ConfigProvider;
use Magento\Framework\App\Http\Context;
use Magento\Store\Model\Store;
use Magento\Framework\App\RequestInterface;

class StoreAjaxProcessor
{
    private $context;

    private $workflowService;

    private $storeManager;

    private $storeCookieManager;

    private $configProvider;

    private $request;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        WorkflowService $workflowService,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        StoreCookieManagerInterface $storeCookieManager
    ) {
        $this->context            = $context;
        $this->configProvider     = $configProvider;
        $this->workflowService    = $workflowService;
        $this->storeManager       = $storeManager;
        $this->request            = $request;
        $this->storeCookieManager = $storeCookieManager;
    }

    /**
     * @param int $storeId
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function process($storeId)
    {
        $relativePageUrl = $this->request->getPostValue('relativePageUrl');

        if (!$storeId) {
            return [];
        }

        if ($storeId == $this->storeManager->getStore()->getId()) {
            return [];
        }

        if ($this->configProvider->isProcessOnFirstVisit() && $this->workflowService->isLocationChanged()) {
            return [];
        }

        if ($this->configProvider->getPopupType() != ConfigProvider::POPUP_TYPE_CONFIRMATION || $this->workflowService->isRequestApproved()) {
            $this->storeManager->setCurrentStore($storeId);
            $store = $this->storeManager->getStore();

            $defaultStoreCode = $this->storeManager->getDefaultStoreView()->getCode();
            $this->context->setValue(Store::ENTITY, $store->getCode(), $defaultStoreCode);
            $this->storeCookieManager->setStoreCookie($store);

            $relativePageUrl = $store->getBaseUrl();
        }

        return [
            'ruleSelector'          => ConfigProvider::RULE_SELECTOR_STORE,
            'popupType'             => $this->configProvider->getPopupType(),
            'redirectUrl'           => $relativePageUrl,
            'storeId'               => $storeId,
            'isLocationChanged'     => (bool)$this->workflowService->isLocationChanged(),
            'isProcessOnFirstVisit' => (bool)$this->configProvider->isProcessOnFirstVisit(),
            'isRequestApproved'     => (bool)$this->workflowService->isRequestApproved(),
            'isRequestRejected'     => (bool)$this->workflowService->isRequestRejected(),
        ];
    }

}
