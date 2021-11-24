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

class Save extends AbstractRule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(RuleInterface::ID);

        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getParams();

        if ($data) {
            $model = $this->initModel();

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            $data = $this->filterPostData($data);
            foreach ($data as $key => $value) {
                if ($key === RuleInterface::ACTIONS) {
                    continue;
                }

                $model->setDataUsingMethod($key, $value);
            }

            try {
                $this->ruleRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the rule.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [RuleInterface::ID => $model->getId()]);
                }

                return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [RuleInterface::ID => $this->getRequest()->getParam(RuleInterface::ID)]
                );
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param array $rawData
     *
     * @return array
     */
    private function filterPostData(array $rawData)
    {
        if (!isset($rawData[RuleInterface::SOURCE_CONDITION])) {
            $rawData[RuleInterface::SOURCE_CONDITION] = RuleInterface::SOURCE_CONDITION_IS;
        }

        foreach ([RuleInterface::SOURCE_TYPE_COUNTRY, RuleInterface::SOURCE_TYPE_LOCALE, RuleInterface::SOURCE_TYPE_IP] as $type) {
            if ($rawData[RuleInterface::SOURCE_TYPE] == $type) {
                $rawData[RuleInterface::SOURCE_VALUE] = $rawData[RuleInterface::SOURCE_VALUE . '_' . $type];

                if (!is_array($rawData[RuleInterface::SOURCE_VALUE])) {
                    $rawData[RuleInterface::SOURCE_VALUE] = explode(',', $rawData[RuleInterface::SOURCE_VALUE]);
                }
            }
        }

        return $rawData;
    }
}
