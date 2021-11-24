<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Plugin\Catalog\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;

/**
 * Plugin Collection
 * plugin name: Amasty_Sorting::SortingMethodsProcessor
 * type: \Magento\Catalog\Model\ResourceModel\Product\Collection
 */
class Collection
{
    const PROCESS_FLAG = 'amsort_process';

    /**
     * @var \Amasty\Sorting\Helper\Data
     */
    private $helper;

    /**
     * @var \Amasty\Sorting\Model\MethodProvider
     */
    private $methodProvider;

    /**
     * @var \Amasty\Sorting\Model\SortingAdapterFactory
     */
    private $adapterFactory;

    /**
     * @var \Amasty\Sorting\Model\ResourceModel\Method\Image
     */
    private $imageMethod;

    /**
     * @var \Amasty\Sorting\Model\ResourceModel\Method\Instock
     */
    private $stockMethod;

    /**
     * @var array
     */
    private $skipAttributes = [];

    public function __construct(
        \Amasty\Sorting\Helper\Data $helper,
        \Amasty\Sorting\Model\MethodProvider $methodProvider,
        \Amasty\Sorting\Model\ResourceModel\Method\Image $imageMethod,
        \Amasty\Sorting\Model\ResourceModel\Method\Instock $stockMethod,
        \Amasty\Sorting\Model\SortingAdapterFactory $adapterFactory
    ) {
        $this->helper         = $helper;
        $this->methodProvider = $methodProvider;
        $this->adapterFactory = $adapterFactory;
        $this->imageMethod    = $imageMethod;
        $this->stockMethod    = $stockMethod;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param string                                                  $attribute
     * @param string                                                  $dir  'ASC'|'DESC'
     *
     * @return array
     */
    public function beforeSetOrder($subject, $attribute, $dir = Select::SQL_DESC)
    {
        $subject->setFlag(self::PROCESS_FLAG, true);
        $this->applyHighPriorityOrders($subject, $dir);
        $flagName = $this->getFlagName($attribute);
        if ($subject->getFlag($flagName)) {
            if ($this->helper->isElasticSort()) {
                $this->skipAttributes[] = $flagName;
            } else {
                // attribute already used in sorting; disable double sorting by renaming attribute into not existing
                $attribute .= '_ignore';
            }
        } else {
            $method = $this->methodProvider->getMethodByCode($attribute);
            if ($method) {
                $method->apply($subject, $dir);
                $attribute = $method->getAlias();
            }
            if (!$this->helper->isElasticSort()) {
                if ($attribute == 'relevance' && !$subject->getFlag($this->getFlagName('am_relevance'))) {
                    $this->addRelevanceSorting($subject, $dir);
                    $attribute = 'am_relevance';
                }
                if ($attribute == 'price') {
                    $subject->addOrder($attribute, $dir);
                    $attribute .= '_ignore';
                }
            }
            $subject->setFlag($flagName, true);
        }

        $subject->setFlag(self::PROCESS_FLAG, false);

        return [$attribute, $dir];
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param callable $proceed
     * @param string $attribute
     * @param string $dir
     * @return mixed
     */
    public function aroundSetOrder($subject, callable $proceed, $attribute, $dir = Select::SQL_DESC)
    {
        $flagName = $this->getFlagName($attribute);
        if (!in_array($flagName, $this->skipAttributes)) {
            $proceed($attribute, $dir);
        }

        return $subject;
    }

    private function getFlagName($attribute)
    {
        if ($attribute == 'price_asc' || $attribute == 'price_desc') {
            $attribute = 'price';
        }
        if (is_string($attribute)) {
            return 'sorted_by_' . $attribute;
        }

        return 'amasty_sorting';
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param string $dir
     */
    private function applyHighPriorityOrders($collection, $dir)
    {
        if (!$collection->getFlag($this->getFlagName('high'))) {
            $this->stockMethod->apply($collection, $dir);
            $this->imageMethod->apply($collection, $dir);
            $collection->setFlag($this->getFlagName('high'), true);
        }
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    private function addRelevanceSorting($collection)
    {
        $collection->getSelect()->columns(['am_relevance' => new \Zend_Db_Expr(
            'search_result.'. TemporaryStorage::FIELD_SCORE
        )]);
        $collection->addExpressionAttributeToSelect('am_relevance', 'am_relevance', []);

        // remove last item from columns because e.am_relevance from addExpressionAttributeToSelect not exist
        $columns = $collection->getSelect()->getPart(\Zend_Db_Select::COLUMNS);
        array_pop($columns);
        $collection->getSelect()->setPart(\Zend_Db_Select::COLUMNS, $columns);
        $collection->setFlag($this->getFlagName('am_relevance'), true);
    }

    /**
     * @param $subject
     * @param $attribute
     * @param string $dir
     * @return array
     */
    public function beforeAddOrder($subject, $attribute, $dir = Select::SQL_DESC)
    {
        if (!$subject->getFlag(self::PROCESS_FLAG)) {
            $result =  $this->beforeSetOrder($subject, $attribute, $dir);
        }

        return $result ?? [$attribute, $dir];
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param $attribute
     * @param string $dir
     * @return mixed
     */
    public function aroundAddOrder($subject, callable $proceed, $attribute, $dir = Select::SQL_DESC)
    {
        return $this->aroundSetOrder($subject, $proceed, $attribute, $dir);
    }
}
