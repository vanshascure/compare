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



namespace Mirasvit\GeoIp\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\GeoIp\Api\Data\RuleInterface;

class Rule extends AbstractModel implements RuleInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\Rule::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceType()
    {
        return $this->getData(self::SOURCE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceType($value)
    {
        return $this->setData(self::SOURCE_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceCondition()
    {
        return $this->getData(self::SOURCE_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceCondition($value)
    {
        return $this->setData(self::SOURCE_CONDITION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceValue()
    {
        try {
            return \Zend_Json::decode($this->getData(self::SOURCE_VALUE));
        } catch (\Exception $e) {
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceValue(array $value)
    {
        $value = array_filter($value);
        foreach ($value as $i => $v) {
            $value[$i] = trim($v);
        }

        $this->setData(self::SOURCE_VALUE, \Zend_Json::encode($value));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($value)
    {
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($value)
    {
        return $this->setData(self::PRIORITY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getActions()
    {
        try {
            return \Zend_Json::decode($this->getData(self::ACTIONS));
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setActions(array $value)
    {
        return $this->setData(self::ACTIONS, \Zend_Json::encode($value));
    }

    /**
     * {@inheritdoc}
     */
    public function isChangeStore()
    {
        return (bool)$this->getActionData(self::ACTION_IS_CHANGE_STORE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsChangeStore($value)
    {
        return $this->setActionData(self::ACTION_IS_CHANGE_STORE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getToStore()
    {
        return $this->getActionData(self::ACTION_TO_STORE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToStore($value)
    {
        return $this->setActionData(self::ACTION_TO_STORE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isChangeCurrency()
    {
        return (bool)$this->getActionData(self::ACTION_IS_CHANGE_CURRENCY);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsChangeCurrency($value)
    {
        return $this->setActionData(self::ACTION_IS_CHANGE_CURRENCY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getToCurrency()
    {
        return $this->getActionData(self::ACTION_TO_CURRENCY);
    }

    /**
     * {@inheritdoc}
     */
    public function setToCurrency($value)
    {
        return $this->setActionData(self::ACTION_TO_CURRENCY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isRedirect()
    {
        return (bool)$this->getActionData(self::ACTION_IS_REDIRECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRedirect($value)
    {
        return $this->setActionData(self::ACTION_IS_REDIRECT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getToRedirectUrl()
    {
        return $this->getActionData(self::ACTION_TO_REDIRECT_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setToRedirectUrl($value)
    {
        return $this->setActionData(self::ACTION_TO_REDIRECT_URL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isRestrict()
    {
        return (bool)$this->getActionData(self::ACTION_IS_RESTRICT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRestrict($value)
    {
        return $this->setActionData(self::ACTION_IS_RESTRICT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getToRestrictUrl()
    {
        return $this->getActionData(self::ACTION_TO_RESTRICT_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setToRestrictUrl($value)
    {
        return $this->setActionData(self::ACTION_TO_RESTRICT_URL, $value);
    }

    /**
     * @param string $key
     *
     * @return bool|string|null
     */
    private function getActionData($key)
    {
        $actions = $this->getActions();

        return isset($actions[$key]) ? $actions[$key] : null;
    }

    /**
     * @param string      $key
     * @param bool|string $value
     *
     * @return RuleInterface
     */
    private function setActionData($key, $value)
    {
        $actions = $this->getActions();

        $actions[$key] = $value;

        return $this->setActions($actions);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds(array $storeIds)
    {
        $storeIdsData = implode(',', $storeIds);
        
        return $this->setData(self::STORE_IDS, $storeIdsData);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return explode(',', $this->getData(self::STORE_IDS));
    }
}
