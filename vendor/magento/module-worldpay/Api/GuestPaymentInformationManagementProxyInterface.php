<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Worldpay\Api;

/**
 * Interface GuestPaymentInformationManagementProxyInterface
 * @api
 * @since 100.1.0
 * @deprecated 100.3.5 Starting from Magento 2.3.5 WorldPay payment method core integration is deprecated
 *       in favor of official payment integration available on the marketplace
 */
interface GuestPaymentInformationManagementProxyInterface
{
    /**
     * Proxy handler for guest place order
     *
     * @param string $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return int Order ID.
     * @since 100.1.0
     */
    public function savePaymentInformationAndPlaceOrder(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    );
}
