<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Block\Adminhtml\System\Config;

class DisableSorting extends \Magento\Config\Block\System\Config\Form\Field
{
    const REPLACE_TARGET = 'category_edit_url';

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return mixed|string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $value = parent::_renderValue($element);
        $value = str_replace(self::REPLACE_TARGET, $this->getUrl('catalog/category/index'), $value);

        return $value;
    }
}
