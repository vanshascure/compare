<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */


namespace Amasty\Acart\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    const XPATH_ONLY_CUSTOMERS = 'general/only_customers';
    const XPATH_DISABLE_LOGGING_FOR_GUESTS = 'general/disable_logging_for_guests';
    const XPATH_EEA_COUNTRIES = 'general/eea_countries';
    const XPATH_PRODUCTS_QTY = 'general/products_qty';
    const XPATH_HISTORY_CLEAN_DAYS = 'general/history_clean_days';
    const XPATH_DEBUG_MODE_EMAIL_DOMAINS = 'debug/debug_emails';
    const XPATH_DEBUG_MODE_ENABLE = 'debug/debug_enable';
    const XPATH_TEST_RECIPIENT = 'testing/recipient_email';
    const XPATH_SAFE_MODE = 'testing/safe_mode';
    const XPATH_EMAIL_TEMPLATES_BCC = 'email_templates/bcc';
    const XPATH_EMAIL_SENDER_NAME = 'email_templates/sender_name';
    const XPATH_REPLY_EMAIL = 'email_templates/reply_email';
    const XPATH_EMAIL_SENDER_IDENTITY = 'email_templates/sender_email_identity';
    const XPATH_EMAIL_TEMPLATES_COPY_METHOD = 'email_templates/copy_method';
    const XPATH_IMG_URL_WITHOUT_PUB = 'email_templates/img_url_without_pub';
    const XPATH_SEND_TO_SUBSCRIBERS_ONLY = 'email_templates/emails_to_newsletter_subscribers_only';

    protected $pathPrefix = 'amasty_acart/';

    /**
     * An alias for scope config with default scope type SCOPE_STORE
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getGlobalValue($key)
    {
        return $this->getValue($key, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isDebugMode(): bool
    {
        return (bool)$this->getGlobalValue(self::XPATH_DEBUG_MODE_ENABLE);
    }

    public function isOnlyCustomers(): bool
    {
        return (bool)$this->getGlobalValue(self::XPATH_ONLY_CUSTOMERS);
    }

    /**
     * @return bool|string
     * @throws \Zend_Validate_Exception
     */
    public function getRecipientEmailForTest()
    {
        $recipientEmail = $this->getGlobalValue(self::XPATH_TEST_RECIPIENT);

        if (empty($recipientEmail) || !\Zend_Validate::is($recipientEmail, 'EmailAddress')) {
            $recipientEmail = false;
        }

        return $recipientEmail;
    }

    public function getDebugEnabledEmailDomains(): array
    {
        if ($this->isDebugMode()) {
            return explode(',', $this->getGlobalValue(self::XPATH_DEBUG_MODE_EMAIL_DOMAINS));
        }

        return [];
    }

    public function getRemovePubFromImgUrl(): bool
    {
        return (bool)$this->getGlobalValue(self::XPATH_IMG_URL_WITHOUT_PUB);
    }

    public function getBcc($storeId): string
    {
        return (string)$this->getValue(self::XPATH_EMAIL_TEMPLATES_BCC, $storeId);
    }

    public function getCopyMethod($storeId): string
    {
        return (string)$this->getValue(self::XPATH_EMAIL_TEMPLATES_COPY_METHOD, $storeId);
    }

    public function getSenderName($storeId = null): string
    {
        return (string)$this->getValue(self::XPATH_EMAIL_SENDER_NAME, $storeId);
    }

    public function getSenderEmail($storeId = null): string
    {
        $emailSenderIdentity = (string)$this->getValue(self::XPATH_EMAIL_SENDER_IDENTITY, $storeId);

        return (string)$this->scopeConfig->getValue(
            'trans_email/ident_' . $emailSenderIdentity . '/email',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getReplyToEmail($storeId = null): string
    {
        return trim($this->getValue(self::XPATH_REPLY_EMAIL, $storeId));
    }

    public function isSafeMode($storeId = null): bool
    {
        return (bool)$this->getValue(self::XPATH_SAFE_MODE, $storeId);
    }

    public function isDisableLoggingForGuests($storeId = null): bool
    {
        return (bool)$this->getValue(self::XPATH_DISABLE_LOGGING_FOR_GUESTS, $storeId);
    }

    public function getProductsQty(): int
    {
        return (int)$this->getValue(self::XPATH_PRODUCTS_QTY);
    }

    public function getHistoryAutoCleanDays(): int
    {
        return (int)$this->getValue(self::XPATH_HISTORY_CLEAN_DAYS);
    }

    public function isEmailsToNewsletterSubscribersOnly($storeId = null): bool
    {
        return (bool)$this->getValue(self::XPATH_SEND_TO_SUBSCRIBERS_ONLY, $storeId);
    }

    public function getEEACountries(): array
    {
        return explode(',', (string)$this->getValue(self::XPATH_EEA_COUNTRIES));
    }
}
