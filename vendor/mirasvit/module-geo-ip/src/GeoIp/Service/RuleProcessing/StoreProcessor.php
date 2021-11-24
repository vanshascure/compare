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
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Mirasvit\GeoIp\Service\WorkflowService;
use Mirasvit\GeoIp\Model\ConfigProvider;
use Magento\Framework\App\Http\Context;

class StoreProcessor
{
    private $context;

    private $workflowService;

    private $storeManager;

    private $storeCookieManager;

    private $jsonFactory;

    private $configProvider;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        JsonFactory $jsonFactory,
        WorkflowService $workflowService,
        StoreManagerInterface $storeManager,
        StoreCookieManagerInterface $storeCookieManager
    ) {
        $this->context            = $context;
        $this->configProvider     = $configProvider;
        $this->jsonFactory        = $jsonFactory;
        $this->workflowService    = $workflowService;
        $this->storeManager       = $storeManager;
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
        if (!$storeId) {
            return [];
        }

        if ($storeId == $this->storeManager->getStore()->getId()) {
            return [];
        }

        if (!$this->workflowService->canLocationChange()) {
            $this->workflowService->requestLocationChange();

            return [];
        }

        $this->storeManager->setCurrentStore($storeId);
        $store = $this->storeManager->getStore();

        $defaultStoreCode = $this->storeManager->getDefaultStoreView()->getCode();
        $this->context->setValue(Store::ENTITY, $store->getCode(), $defaultStoreCode);
        $this->storeCookieManager->setStoreCookie($store);

        $this->workflowService->submitLocationChange();

        return [];
    }
}
