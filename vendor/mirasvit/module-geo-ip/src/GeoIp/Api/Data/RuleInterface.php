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



namespace Mirasvit\GeoIp\Api\Data;

interface RuleInterface
{
    const SOURCE_TYPE_COUNTRY = 'country';
    const SOURCE_TYPE_LOCALE  = 'locale';
    const SOURCE_TYPE_IP      = 'ip';

    const SOURCE_CONDITION_IS     = '==';
    const SOURCE_CONDITION_IS_NOT = '!=';

    const ACTION_IS_CHANGE_STORE = 'is_change_store';
    const ACTION_TO_STORE        = 'to_store';

    const ACTION_IS_CHANGE_CURRENCY = 'is_change_currency';
    const ACTION_TO_CURRENCY        = 'to_currency';

    const ACTION_IS_REDIRECT     = 'is_redirect';
    const ACTION_TO_REDIRECT_URL = 'to_redirect_url';

    const ACTION_IS_RESTRICT     = 'is_restrict';
    const ACTION_TO_RESTRICT_URL = 'to_restrict_url';

    const TABLE_NAME = 'mst_geo_ip_rule';

    const ID               = 'rule_id';
    const NAME             = 'name';
    const DESCRIPTION      = 'description';
    const SOURCE_TYPE      = 'source_type';
    const SOURCE_CONDITION = 'source_condition';
    const SOURCE_VALUE     = 'source_value';
    const IS_ACTIVE        = 'is_active';
    const PRIORITY         = 'priority';
    const ACTIONS          = 'actions';
    const STORE_IDS        = 'store_ids';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDescription($value);

    /**
     * @return string
     */
    public function getSourceType();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSourceType($value);

    /**
     * @return string
     */
    public function getSourceCondition();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSourceCondition($value);

    /**
     * @return array
     */
    public function getSourceValue();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setSourceValue(array $value);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsActive($value);

    /**
     * @return string
     */
    public function getPriority();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPriority($value);

    /**
     * @return array
     */
    public function getActions();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setActions(array $value);

    /**
     * @return bool
     */
    public function isChangeStore();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsChangeStore($value);

    /**
     * @return string
     */
    public function getToStore();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setToStore($value);

    /**
     * @return bool
     */
    public function isChangeCurrency();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsChangeCurrency($value);

    /**
     * @return string
     */
    public function getToCurrency();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setToCurrency($value);

    /**
     * @return bool
     */
    public function isRedirect();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsRedirect($value);

    /**
     * @return string
     */
    public function getToRedirectUrl();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setToRedirectUrl($value);

    /**
     * @return bool
     */
    public function isRestrict();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsRestrict($value);

    /**
     * @return string
     */
    public function getToRestrictUrl();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setToRestrictUrl($value);

    /**
     * @param string $key
     *
     * @return array
     */
    public function getData($key = null);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getDataUsingMethod($key);

    /**
     * @param string       $key
     * @param string|array $args
     *
     * @return RuleInterface
     */
    public function setDataUsingMethod($key, $args = []);

    /**
     * @param array $value
     * @return $this
     */
    public function setStoreIds(array $value);

    /**
     * @return array
     */
    public function getStoreIds();
}