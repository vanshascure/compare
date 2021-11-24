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

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\GeoIp\Service\WorkflowService;

class CurrencyProcessor
{
    private $workflowService;

    private $storeManager;

    public function __construct(
        WorkflowService $workflowService,
        StoreManagerInterface $storeManager
    ) {
        $this->workflowService = $workflowService;
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
        if (!$currencyCode) {
            return [];
        }

        /** @var Store $store */
        $store = $this->storeManager->getStore();

        if ($store->getCurrentCurrencyCode() === $currencyCode) {
            return [];
        }

        if (!$this->workflowService->canCurrencyChange()) {
            return [];
        }

        $store->setCurrentCurrencyCode($currencyCode);

        $this->workflowService->submitCurrencyChange();

        return [];
    }
}
