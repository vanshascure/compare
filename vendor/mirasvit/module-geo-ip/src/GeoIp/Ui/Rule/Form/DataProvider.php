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



namespace Mirasvit\GeoIp\Ui\Rule\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\GeoIp\Api\Data\RuleInterface;
use Mirasvit\GeoIp\Repository\RuleRepository;

class DataProvider extends AbstractDataProvider
{

    private $ruleRepository;

    /**
     * DataProvider constructor.
     *
     * @param RuleRepository $ruleRepository
     * @param string         $name
     * @param string         $primaryFieldName
     * @param string         $requestFieldName
     * @param array          $meta
     * @param array          $data
     */
    public function __construct(
        RuleRepository $ruleRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->collection     = $this->ruleRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->ruleRepository->getCollection() as $rule) {
            $ruleData = $rule->getData();

            $ruleData[RuleInterface::SOURCE_VALUE . '_' . $rule->getSourceType()] = $rule->getSourceValue();

            $ruleData[RuleInterface::ACTION_IS_CHANGE_STORE]    = $rule->isChangeStore() ? "1" : "0";
            $ruleData[RuleInterface::ACTION_IS_CHANGE_CURRENCY] = $rule->isChangeCurrency() ? "1" : "0";
            $ruleData[RuleInterface::ACTION_IS_REDIRECT]        = $rule->isRedirect() ? "1" : "0";
            $ruleData[RuleInterface::ACTION_IS_RESTRICT]        = $rule->isRestrict() ? "1" : "0";

            $ruleData[RuleInterface::ACTION_TO_STORE]        = $rule->getToStore();
            $ruleData[RuleInterface::ACTION_TO_CURRENCY]     = $rule->getToCurrency();
            $ruleData[RuleInterface::ACTION_TO_REDIRECT_URL] = $rule->getToRedirectUrl();
            $ruleData[RuleInterface::ACTION_TO_RESTRICT_URL] = $rule->getToRestrictUrl();

            $result[$rule->getId()] = $ruleData;
        }

        return $result;
    }
}
