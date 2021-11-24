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
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\GeoIp\Api\Data\RuleInterface;
use Mirasvit\GeoIp\Model\ConfigProvider;
use Mirasvit\GeoIp\Repository\RuleRepository;


class RuleProcessorService
{
    private $ruleRepository;

    private $workflowService;

    private $lookupService;

    private $configProvider;

    private $actionProcessors;

    private $request;

    private $storeManager;

    public function __construct(
        RequestInterface $request,
        RuleRepository $ruleRepository,
        WorkflowService $workflowService,
        LookupService $lookupService,
        ConfigProvider $configProvider,
        ActionProcessors $actionProcessors,
        StoreManagerInterface $storeManager
    ) {
        $this->request          = $request;
        $this->ruleRepository   = $ruleRepository;
        $this->workflowService  = $workflowService;
        $this->lookupService    = $lookupService;
        $this->configProvider   = $configProvider;
        $this->actionProcessors = $actionProcessors;
        $this->storeManager     = $storeManager;
    }

    /**
     * @return array
     */
    public function processAjax()
    {
        if (!$this->isAllowed()) {
            return [];
        }
        $actions    = $this->getActions();
        $processors = $this->actionProcessors->ajaxProcessors();
        $result     = $this->applyActions($actions, $processors);
        if (!is_array($result)) {
            return [];
        }

        return $result;
    }

    public function process()
    {
        if (!$this->isAllowed()) {
            return false;
        }

        //        $actions = $this->filterActions($actions);

        $actions    = $this->getActions();
        $processors = $this->actionProcessors->processors();

        return $this->applyActions($actions, $processors);
    }

    private function getActions()
    {
        $collection = $this->ruleRepository->getCollection();
        $collection->addFieldToFilter(RuleInterface::IS_ACTIVE, true)
            ->setOrder(RuleInterface::PRIORITY, \Zend_Db_Select::SQL_ASC);

        $actions = [
            RuleInterface::ACTION_TO_STORE        => false,
            RuleInterface::ACTION_TO_CURRENCY     => false,
            RuleInterface::ACTION_TO_REDIRECT_URL => false,
            RuleInterface::ACTION_TO_RESTRICT_URL => false,
        ];

        foreach ($collection as $rule) {
            if ($this->isApplicable($rule)) {
                if ($rule->isChangeStore()) {
                    $actions[RuleInterface::ACTION_TO_STORE] = $rule->getToStore();
                }

                if ($rule->isChangeCurrency()) {
                    $actions[RuleInterface::ACTION_TO_CURRENCY] = $rule->getToCurrency();
                }

                if ($rule->isRedirect()) {
                    $actions[RuleInterface::ACTION_TO_REDIRECT_URL] = $rule->getToRedirectUrl();
                }

                if ($rule->isRestrict()) {
                    $actions[RuleInterface::ACTION_TO_RESTRICT_URL] = $rule->getToRestrictUrl();
                }
            }
        }

        return $actions;
    }

    //    private function filterActions(array $actions)
    //    {
    //        if ($this->configProvider->getPopupType() !== ConfigProvider::POPUP_TYPE_NONE) {
    //
    //            if ($actions[RuleInterface::ACTION_TO_STORE] || $actions[RuleInterface::ACTION_TO_REDIRECT_URL]) {
    //                $actions[RuleInterface::ACTION_TO_STORE]        = false;
    //                $actions[RuleInterface::ACTION_TO_REDIRECT_URL] = false;
    //            }
    //        }
    //
    //        return $actions;
    //    }

    private function isAllowed()
    {
        if (!$this->configProvider->isEnabled()) {
            return false;
        }

        $ip = $this->lookupService->getIp();

        if (in_array($ip, $this->configProvider->getLimitationIgnoredIps())) {
            return false;
        }

        $userAgent = $this->lookupService->getUserAgent();
        foreach ($this->configProvider->getLimitationAgents() as $line) {
            $line = trim(strtolower($line));
            if ($line && strpos($userAgent, $line) !== false) {
                return false;
            }
        }

        return true;
    }

    private function isApplicable(RuleInterface $rule)
    {
        $currentStoreId = $this->storeManager->getStore()->getId();

        if(!$this->isApplicableByStore($rule, $currentStoreId)) {
            return false;
        }

        switch ($rule->getSourceType()) {
            case RuleInterface::SOURCE_TYPE_COUNTRY:
                //return true; // uncomment for test
                return in_array($this->lookupService->getCountry(), $rule->getSourceValue());

            case RuleInterface::SOURCE_TYPE_LOCALE:
                $locale = $this->lookupService->getLocale();
                if (strpos($locale, '*') !== false) {
                    $parts    = explode('_', $locale);
                    $langCode = $parts[0];
                    foreach ($rule->getSourceValue() as $ruleLocale) {
                        $ruleLocaleParts = explode('_', $ruleLocale);
                        $ruleLangCode    = $ruleLocaleParts[0];
                        if ($langCode == $ruleLangCode) {
                            return true;
                        }
                    }
                }

                return in_array($locale, $rule->getSourceValue());

            case RuleInterface::SOURCE_TYPE_IP:
                return in_array($this->lookupService->getIp(), $rule->getSourceValue());
        }

        return false;
    }

    /**
     * @param RuleInterface $rule
     * @param int           $storeId
     * @return bool
     */
    private function isApplicableByStore(RuleInterface $rule, $storeId)
    {
        $ruleStoreIds = $rule->getStoreIds();

        return in_array(0, $ruleStoreIds) || in_array($storeId, $ruleStoreIds);
    }

    private function applyActions(array $actions, array $processors)
    {
        foreach ($processors as $key => $processor) {
            if ($actions[$key]) {
                $result = $processor->process($actions[$key]);
                if (!empty($result)) {
                    return $result;
                }
            }
        }

        return false;
    }
}
