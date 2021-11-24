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



namespace Mirasvit\GeoIp\Ui\Rule\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\GeoIp\Api\Data\RuleInterface;
use Mirasvit\GeoIp\Model\ConfigProvider;

class SourceTypeSource implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Country'),
                'value' => RuleInterface::SOURCE_TYPE_COUNTRY
            ],
            [
                'label' => __('Browser Locale'),
                'value' => RuleInterface::SOURCE_TYPE_LOCALE
            ],
            [
                'label' => __('IP'),
                'value' => RuleInterface::SOURCE_TYPE_IP
            ],
        ];
    }
}