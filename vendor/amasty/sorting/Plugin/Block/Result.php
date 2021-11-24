<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Plugin\Block;

use Amasty\Sorting\Helper\Data;
use Magento\CatalogSearch\Block\Result as Subject;
use Magento\Framework\Registry;

class Result
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Data $helper, Registry $registry)
    {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    /**
     * @param Subject $result
     * @return $this
     */
    public function afterSetListOrders(Subject $result)
    {
        $searchSortings = $this->helper->getSearchSorting();
        // getting first default sorting
        $sortBy = array_shift($searchSortings);
        $result->getListBlock()->setDefaultSortBy(
            $sortBy
        );
        $this->registry->unregister(Data::SEARCH_SORTING);
        $this->registry->register(Data::SEARCH_SORTING, true);

        return $this;
    }
}
