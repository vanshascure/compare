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
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\GeoIp\Service\WorkflowService;

class Redirect extends Action
{
    private $storeCookieManager;

    private $storeManager;

    private $httpContext;

    private $workflowService;

    public function __construct(
        WorkflowService $workflowService,
        HttpContext $httpContext,
        StoreManagerInterface $storeManager,
        StoreCookieManagerInterface $storeCookieManager,
        Context $context
    ) {
        $this->workflowService    = $workflowService;
        $this->httpContext        = $httpContext;
        $this->storeManager       = $storeManager;
        $this->storeCookieManager = $storeCookieManager;

        parent::__construct($context);
    }

    public function execute()
    {
        $redirectUrl = $this->getRequest()->getParam('redirectUrl', '/');
        $storeId     = (int)$this->getRequest()->getParam('storeId');

        if ($storeId) {
            $this->storeManager->setCurrentStore($storeId);
            $store            = $this->storeManager->getStore();
            $defaultStoreCode = $this->storeManager->getDefaultStoreView()->getCode();
            $this->httpContext->setValue(Store::ENTITY, $store->getCode(), $defaultStoreCode);
            $this->storeCookieManager->setStoreCookie($store);
        }

        $this->workflowService->setRequestStatus('accept');
        $this->workflowService->submitLocationChange();

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setUrl($redirectUrl);

        return $redirect;
    }
}
