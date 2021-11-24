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

use Mirasvit\GeoIp\Service\RuleProcessing;
use Mirasvit\GeoIp\Api\Data\RuleInterface;

class ActionProcessors
{
    private $storeProcessor;

    private $currencyProcessor;

    private $redirectProcessor;

    private $restrictProcessor;


    private $storeAjaxProcessor;

    private $currencyAjaxProcessor;

    private $redirectAjaxProcessor;

    private $restrictAjaxProcessor;


    public function __construct(
        RuleProcessing\StoreProcessor $storeProcessor,
        RuleProcessing\CurrencyProcessor $currencyProcessor,
        RuleProcessing\RedirectProcessor $redirectProcessor,
        RuleProcessing\RestrictProcessor $restrictProcessor,
        RuleProcessing\StoreAjaxProcessor $storeAjaxProcessor,
        RuleProcessing\CurrencyAjaxProcessor $currencyAjaxProcessor,
        RuleProcessing\RedirectAjaxProcessor $redirectAjaxProcessor,
        RuleProcessing\RestrictAjaxProcessor $restrictAjaxProcessor
    ) {
        $this->storeProcessor    = $storeProcessor;
        $this->currencyProcessor = $currencyProcessor;
        $this->redirectProcessor = $redirectProcessor;
        $this->restrictProcessor = $restrictProcessor;

        $this->storeAjaxProcessor    = $storeAjaxProcessor;
        $this->currencyAjaxProcessor = $currencyAjaxProcessor;
        $this->redirectAjaxProcessor = $redirectAjaxProcessor;
        $this->restrictAjaxProcessor = $restrictAjaxProcessor;
    }

    public function processors()
    {
        return [
            RuleInterface::ACTION_TO_STORE        => $this->storeProcessor,
            RuleInterface::ACTION_TO_CURRENCY     => $this->currencyProcessor,
            RuleInterface::ACTION_TO_REDIRECT_URL => $this->redirectProcessor,
            RuleInterface::ACTION_TO_RESTRICT_URL => $this->restrictProcessor,
        ];
    }

    public function ajaxProcessors()
    {
        return [
            RuleInterface::ACTION_TO_STORE        => $this->storeAjaxProcessor,
            RuleInterface::ACTION_TO_CURRENCY     => $this->currencyAjaxProcessor,
            RuleInterface::ACTION_TO_REDIRECT_URL => $this->redirectAjaxProcessor,
            RuleInterface::ACTION_TO_RESTRICT_URL => $this->restrictAjaxProcessor,
        ];
    }

}
