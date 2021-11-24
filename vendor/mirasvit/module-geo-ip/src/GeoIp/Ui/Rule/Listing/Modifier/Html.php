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



namespace Mirasvit\GeoIp\Ui\Rule\Listing\Modifier;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\GeoIp\Api\Data\RuleInterface;
use Mirasvit\GeoIp\Model\Source\CurrencySource;
use Mirasvit\GeoIp\Model\Source\StoreViewSource;
use Mirasvit\GeoIp\Ui\Rule\Source\CountrySource;
use Mirasvit\GeoIp\Ui\Rule\Source\LocaleSource;
use Mirasvit\GeoIp\Ui\Rule\Source\SourceTypeSource;

class Html
{
    private $sourceTypeSource;

    private $countrySource;

    private $localeSource;

    private $storeViewSource;

    private $currencySource;

    private $storeManager;

    public function __construct(
        SourceTypeSource $sourceTypeSource,
        CountrySource $countrySource,
        LocaleSource $localeSource,
        StoreViewSource $storeViewSource,
        CurrencySource $currencySource,
        StoreManagerInterface $storeManager
    ) {
        $this->sourceTypeSource = $sourceTypeSource;
        $this->countrySource    = $countrySource;
        $this->localeSource     = $localeSource;
        $this->storeViewSource  = $storeViewSource;
        $this->currencySource   = $currencySource;
        $this->storeManager     = $storeManager;
    }

    public function modify(RuleInterface $rule, array $data)
    {
        $data['rule_conditions'] = $this->conditionsHtml($rule);
        $data['rule_actions']    = $this->actionsHtml($rule);
        $data['store_ids']       = $this->storeIdsHtml($rule);

        return $data;
    }

    private function conditionsHtml(RuleInterface $rule)
    {
        $source = $this->getOptionLabel($this->sourceTypeSource, $rule->getSourceType());
        $values = [];
        if ($rule->getSourceType() === RuleInterface::SOURCE_TYPE_COUNTRY) {
            $values = $this->getOptionLabels($this->countrySource, $rule->getSourceValue());
        } elseif ($rule->getSourceType() === RuleInterface::SOURCE_TYPE_LOCALE) {
            $values = $this->getOptionLabels($this->localeSource, $rule->getSourceValue());
        } elseif ($rule->getSourceType() === RuleInterface::SOURCE_TYPE_IP) {
            $values = $rule->getSourceValue();
        }

        $assembled = [];
        foreach ($values as $idx => $item) {
            $assembled[] = '<i>' . $item . '</i>';

            if ($idx > 5) {
                $assembled[] = '<i>+' . (count($values) - $idx) . '...</i>';
                break;
            }
        }

        return sprintf('
            <div class="mst_geo-ip__rule-html">
                <div>
                    <strong>%s</strong>
                    %s
                </div>
            </div>
        ', $source, implode('', $assembled));
    }

    private function actionsHtml(RuleInterface $rule)
    {
        $lines = [];

        if ($rule->isChangeStore()) {
            $lines['Store'] = $this->getOptionLabel($this->storeViewSource, $rule->getToStore());
        }

        if ($rule->isChangeCurrency()) {
            $lines['Currency'] = $this->getOptionLabel($this->currencySource, $rule->getToCurrency());
        }

        if ($rule->isRedirect()) {
            $lines['Redirect'] = $rule->getToRedirectUrl();
        }

        if ($rule->isRestrict()) {
            $lines['Restrict Access'] = $rule->getToRestrictUrl();
        }

        $assembled = [];
        foreach ($lines as $idx => $value) {
            $assembled[] = '<div><strong>' . $idx . '</strong>' . '<i>' . $value . '</i></div>';
        }

        return sprintf('
            <div class="mst_geo-ip__rule-html">
                %s
            </div>
        ', implode('', $assembled));
    }

    /**
     * @param OptionSourceInterface $options
     * @param string                $value
     *
     * @return string
     */
    private function getOptionLabel(OptionSourceInterface $options, $value)
    {
        foreach ($options->toOptionArray() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return '';
    }

    private function getOptionLabels(OptionSourceInterface $options, array $value)
    {
        $result = [];
        foreach ($options->toOptionArray() as $option) {
            if (in_array($option['value'], $value)) {
                $result[] = $option['label'];
            }
        }

        return $result;
    }

    private function storeIdsHtml(RuleInterface $rule)
    {
        $stores = [];
        foreach ($rule->getStoreIds() as $storeId) {
            $storeLabel = $storeId == 0
                ? 'All Store Views'
                : $this->storeManager->getStore($storeId)->getName();
            $stores[] = '<div><i>' . $storeLabel . '</i></div>';
        }

        return sprintf('
            <div class="mst_geo-ip__rule-html">
                %s
            </div>
        ', implode('', $stores));
    }
}
