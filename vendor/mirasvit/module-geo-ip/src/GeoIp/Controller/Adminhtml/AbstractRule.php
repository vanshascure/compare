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



namespace Mirasvit\GeoIp\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\GeoIp\Api\Data\RuleInterface;
use Mirasvit\GeoIp\Repository\RuleRepository;

abstract class AbstractRule extends Action
{
    protected $ruleRepository;

    protected $context;

    public function __construct(
        RuleRepository $ruleRepository,
        Context $context
    ) {
        $this->ruleRepository = $ruleRepository;
        $this->context        = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Backend::system');
        $resultPage->getConfig()->getTitle()->prepend(__('GeoIP'));
        $resultPage->getConfig()->getTitle()->prepend(__('GeoIP Rules'));

        return $resultPage;
    }

    /**
     * @return RuleInterface
     */
    public function initModel()
    {
        $model = $this->ruleRepository->create();

        if ($this->getRequest()->getParam(RuleInterface::ID)) {
            $model = $this->ruleRepository->get($this->getRequest()->getParam(RuleInterface::ID));
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_GeoIp::geo_ip');
    }
}
