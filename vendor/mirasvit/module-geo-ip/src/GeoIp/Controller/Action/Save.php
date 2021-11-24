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



namespace Mirasvit\GeoIp\Controller\Action;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Api\StoreCookieManagerInterface;
use Mirasvit\GeoIp\Service\WorkflowService;
use Mirasvit\GeoIp\Model\ConfigProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Controller\Result\JsonFactory;

class Save extends Action
{
    private $workflowService;

    private $storeManager;

    private $httpContext;

    private $storeCookieManager;

    private $jsonFactory;

    public function __construct(
        WorkflowService $workflowService,
        StoreManagerInterface $storeManager,
        JsonFactory $jsonFactory,
        HttpContext $httpContext,
        StoreCookieManagerInterface $storeCookieManager,
        Context $context
    ) {
        $this->workflowService    = $workflowService;
        $this->storeManager       = $storeManager;
        $this->jsonFactory        = $jsonFactory;
        $this->httpContext        = $httpContext;
        $this->storeCookieManager = $storeCookieManager;
        parent::__construct($context);
    }

    /**
     * * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $ruleSelector = $this->getRequest()->getParam('ruleSelector');
        $resultChange = $this->getRequest()->getParam('result');

        $data = [];

        if ($ruleSelector == ConfigProvider::RULE_SELECTOR_REDIRECT) {
            if ($resultChange == 'done') {
                $this->workflowService->submitLocationChange();
            }
            if ($resultChange == 'accept') {
                $this->workflowService->submitLocationChange();
                $this->workflowService->setRequestStatus('accept');
            }
            if ($resultChange == 'reject') {
                $this->workflowService->setRequestStatus('reject');
            }
        }

        if ($ruleSelector == ConfigProvider::RULE_SELECTOR_CURRENCY) {
            if ($resultChange == 'done') {
                $this->workflowService->submitCurrencyChange();
            }
        }

        if ($ruleSelector == ConfigProvider::RULE_SELECTOR_STORE) {
            if ($resultChange == 'accept') {
                $storeId = (int)$this->getRequest()->getParam('storeId');
                if ($storeId) {
                    $this->workflowService->submitLocationChange();
                    $this->workflowService->setRequestStatus('accept');

                    $this->storeManager->setCurrentStore($storeId);
                    $store = $this->storeManager->getStore();

                    $data['redirectUrl'] = $store->getBaseUrl();

                    $defaultStoreCode = $this->storeManager->getDefaultStoreView()->getCode();
                    $this->httpContext->setValue(Store::ENTITY, $store->getCode(), $defaultStoreCode);
                    $this->storeCookieManager->setStoreCookie($store);
                }
            }
            if ($resultChange == 'reject') {
                $this->workflowService->setRequestStatus('reject');
            }
        }

        $data['status_after_save'] = [
            'ruleSelector'      => $ruleSelector,
            'isLocationChanged' => $this->workflowService->isLocationChanged(),
            'isRequestApproved' => $this->workflowService->isRequestApproved(),
            'isRequestRejected' => $this->workflowService->isRequestRejected(),
        ];

        return $this->jsonFactory->create()->setData([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
