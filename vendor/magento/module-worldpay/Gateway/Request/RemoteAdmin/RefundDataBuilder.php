<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Gateway\Request\RemoteAdmin;

use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class RefundDataBuilder
 *
 * @deprecated 100.3.5 Starting from Magento 2.3.5 WorldPay payment method core integration is deprecated
 *       in favor of official payment integration available on the marketplace
 */
class RefundDataBuilder extends TransactionDataBuilder
{
    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = SubjectReader::readPayment($buildSubject);

        /** @var Payment $paymentInfo */
        $paymentInfo = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($paymentInfo);

        return array_merge(
            parent::build($buildSubject),
            [
                'cartId' => 'Refund',
                'op' => 'refund-partial',
                'transId' => $paymentInfo->getParentTransactionId(),
                'amount' => sprintf('%.2F', SubjectReader::readAmount($buildSubject)),
                'currency' => $paymentDO->getOrder()->getCurrencyCode()
            ]
        );
    }
}
