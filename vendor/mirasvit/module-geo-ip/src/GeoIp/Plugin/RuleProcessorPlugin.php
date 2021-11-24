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



namespace Mirasvit\GeoIp\Plugin;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Mirasvit\GeoIp\Service\RuleProcessorService;
use Mirasvit\GeoIp\Model\ConfigProvider;

/**
 * @see \Magento\Framework\App\FrontController
 */
class RuleProcessorPlugin
{
    private $ruleProcessorService;

    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider,
        RuleProcessorService $ruleProcessorService
    ) {
        $this->configProvider       = $configProvider;
        $this->ruleProcessorService = $ruleProcessorService;
    }

    /**
     * @param FrontControllerInterface $subject
     * @param \Closure                 $proceed
     * @param RequestInterface         $request
     *
     * @return bool|$this
     */
    public function aroundDispatch(
        $subject,
        \Closure $proceed,
        RequestInterface $request
    ) {
        /** @var \Magento\Framework\App\Request\Http $request */
        if ($request->isGet() === false) {
            return $proceed($request);
        }

        if ($this->configProvider->isAjaxMode()) {
            return $proceed($request);
        }

        $result = $this->ruleProcessorService->process();
        if (!empty($result)) {
            return $result;
        }

        return $proceed($request);
    }
}
