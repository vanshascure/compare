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



namespace Mirasvit\GeoIp\Block;

use Magento\Framework\View\Element\Template;
use Mirasvit\GeoIp\Model\ConfigProvider;
use Mirasvit\GeoIp\Service\UrlService;
use Mirasvit\GeoIp\Service\WorkflowService;

class Js extends Template
{
    private $urlService;

    private $workflowService;

    private $configProvider;

    public function __construct(
        UrlService $urlService,
        WorkflowService $workflowService,
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        $this->urlService      = $urlService;
        $this->workflowService = $workflowService;
        $this->configProvider  = $configProvider;

        parent::__construct($context, $data);
    }

    public function getJsConfigProcess()
    {
        $config = [];

        $config['Magento_Ui/js/core/app']['components']['geoIpProcess'] = [
            'component' => 'Mirasvit_GeoIp/js/process',
            'config'    => [
                'ajaxMode'     => $this->configProvider->isAjaxMode(),
                'urlProcess'   => $this->getUrl('geo_ip/action/process'),
                'urlResult'    => $this->getUrl('geo_ip/action/save'),
                'redirectUrls' => $this->urlService->getRedirectUrls(),

                'popupTypeNone'         => ConfigProvider::POPUP_TYPE_NONE,
                'popupTypeNotification' => ConfigProvider::POPUP_TYPE_NOTIFICATION,
                'popupTypeConfirmation' => ConfigProvider::POPUP_TYPE_CONFIRMATION,

                'ruleSelectorRedirect' => ConfigProvider::RULE_SELECTOR_REDIRECT,
                'ruleSelectorCurrency' => ConfigProvider::RULE_SELECTOR_CURRENCY,
                'ruleSelectorRestrict' => ConfigProvider::RULE_SELECTOR_RESTRICT,
                'ruleSelectorStore'    => ConfigProvider::RULE_SELECTOR_STORE,

            ],
        ];

        return ['*' => $config];
    }

    /**
     * @return array|false
     */
    public function getJsConfigPopup()
    {
        $config = [];

        $config['Magento_Ui/js/core/app']['components']['geoIpPopup'] = [
            'component' => 'Mirasvit_GeoIp/js/popup',
            'config'    => [
                'popup' => [
                    'type'      => $this->configProvider->getPopupType(),
                    'title'     => (string)__('Redirect notification'),
                    'text'      => $this->configProvider->getPopupText(),
                    'acceptUrl' => $this->getUrl('geo_ip/action/accept'),
                    'rejectUrl' => $this->getUrl('geo_ip/action/reject'),

                    'ajaxMode'              => $this->configProvider->isAjaxMode(),
                    'isShowRequest'         => $this->workflowService->isShowRequest(),
                    'popupTypeNone'         => ConfigProvider::POPUP_TYPE_NONE,
                    'popupTypeNotification' => ConfigProvider::POPUP_TYPE_NOTIFICATION,
                    'popupTypeConfirmation' => ConfigProvider::POPUP_TYPE_CONFIRMATION,
                ],
            ],
        ];

        return ['*' => $config];
    }

}
