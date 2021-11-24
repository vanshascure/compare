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



namespace Mirasvit\GeoIp\Controller\Action;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\GeoIp\Service\RuleProcessorService;
use Magento\Framework\Controller\Result\JsonFactory;

class Process extends Action
{
    private $ruleProcessorService;

    private $jsonFactory;

    public function __construct(
        RuleProcessorService $ruleProcessorService,
        JsonFactory $jsonFactory,
        Context $context
    ) {
        $this->ruleProcessorService = $ruleProcessorService;
        $this->jsonFactory          = $jsonFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $data = $this->ruleProcessorService->processAjax();

        return $this->jsonFactory->create()->setData([
            'success' => true,
            'data'    => $data,
        ]);
    }
}
