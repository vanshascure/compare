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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Mirasvit\GeoIp\Service\WorkflowService;
use Mirasvit\GeoIp\Model\ConfigProvider;
use Mirasvit\GeoIp\Service\UrlService;

class RedirectAjaxProcessor
{
    private $urlService;

    private $workflowService;

    private $request;

    private $response;

    private $configProvider;

    public function __construct(
        UrlService $urlService,
        ConfigProvider $configProvider,
        WorkflowService $workflowService,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $this->urlService      = $urlService;
        $this->configProvider  = $configProvider;
        $this->workflowService = $workflowService;
        $this->request         = $request;
        $this->response        = $response;
    }

    /**
     * @param string $redirectUrl
     *
     * @return array
     */
    public function process($redirectUrl)
    {
        $relativePageUrl = $this->request->getPostValue('relativePageUrl');

        if (!$redirectUrl) {
            return [];
        }

        if ($this->urlService->isEquivalentUrl($relativePageUrl, $redirectUrl)) {
            return [];
        }

        if ($this->configProvider->isProcessOnFirstVisit() && $this->workflowService->isLocationChanged()) {
            return [];
        }

        return [
            'ruleSelector'          => ConfigProvider::RULE_SELECTOR_REDIRECT,
            'popupType'             => $this->configProvider->getPopupType(),
            'redirectUrl'           => $redirectUrl,
            'isLocationChanged'     => (bool)$this->workflowService->isLocationChanged(),
            'isProcessOnFirstVisit' => (bool)$this->configProvider->isProcessOnFirstVisit(),
            'isRequestApproved'     => (bool)$this->workflowService->isRequestApproved(),
            'isRequestRejected'     => (bool)$this->workflowService->isRequestRejected(),
        ];

    }

}
