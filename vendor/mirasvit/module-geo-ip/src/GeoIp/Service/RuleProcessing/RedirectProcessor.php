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
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Mirasvit\GeoIp\Service\WorkflowService;

class RedirectProcessor
{
    private $workflowService;

    private $request;

    private $response;

    private $redirectFactory;

    public function __construct(
        WorkflowService $workflowService,
        RequestInterface $request,
        ResponseInterface $response,
        RedirectFactory $redirectFactory
    ) {
        $this->workflowService = $workflowService;
        $this->request         = $request;
        $this->response        = $response;
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @param string $url
     *
     * @return array|Redirect
     */
    public function process($url)
    {
        if (!$url) {
            return [];
        }

        if ($this->request->getRequestUri() === $url) {
            return [];
        }

        if (!$this->workflowService->canLocationChange()) {
            $this->workflowService->requestLocationChange();

            return [];
        }

        $this->response->setNoCacheHeaders();

        $redirect = $this->redirectFactory->create();
        $redirect->setUrl($url);

        $this->workflowService->submitLocationChange();

        return $redirect;
    }

}
