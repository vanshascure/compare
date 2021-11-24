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



namespace Mirasvit\GeoIp\Controller\Adminhtml\Rule;

use Mirasvit\GeoIp\Api\Data\RuleInterface;
use Mirasvit\GeoIp\Controller\Adminhtml\AbstractRule;

class Duplicate extends AbstractRule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                $clone = $this->ruleRepository->create();

                foreach (array_keys($model->getData()) as $key) {
                    if ($key === RuleInterface::ID) {
                        continue;
                    }

                    $clone->setDataUsingMethod($key, $model->getDataUsingMethod($key));
                }
                $clone->setIsActive(false)
                    ->setName($clone->getName() . ' Copy');

                $this->ruleRepository->save($clone);

                $this->messageManager->addSuccessMessage(__('The rule has been duplicated.'));

                return $resultRedirect->setPath('*/*/edit', [RuleInterface::ID => $clone->getId()]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*');
            }
        } else {
            $this->messageManager->addErrorMessage(__('This rule no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
