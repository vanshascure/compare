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



namespace Mirasvit\GeoIp\Model\Source;

use Magento\CurrencySymbol\Model\System\Currencysymbol;
use Magento\Framework\Data\OptionSourceInterface;

class CurrencySource implements OptionSourceInterface
{
    private $currencySymbol;

    public function __construct(
        Currencysymbol $currencySymbol
    ) {
        $this->currencySymbol = $currencySymbol;
    }

    public function toOptionArray()
    {
        $result = [];

        foreach ($this->currencySymbol->getCurrencySymbolsData() as $code => $item) {
            if (isset($item['displayName'])) {
                $result[] = [
                    'label' => $item['displayName'],
                    'value' => $code,
                ];
            }
        }

        return $result;
    }
}