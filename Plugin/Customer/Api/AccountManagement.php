<?php

declare(strict_types=1);

namespace MageMonk\SocialSignOn\Plugin\Customer\Api;

use Magento\Customer\Api\AccountManagementInterface;
use MageMonk\SocialSignOn\Model\Customer\SessionSourceKey;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Plugin to update customer data for Google sign up
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AccountManagement
{
    /**
     * Initialization
     *
     * @param CustomerSession $customerSession
     */
    public function __construct(
        private readonly CustomerSession $customerSession
    ) {
    }

    /**
     * Before create account
     *
     * @param AccountManagementInterface $subject
     * @param CustomerInterface $customer
     * @param string|null $password
     * @param string|null $redirectUrl
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeCreateAccount(
        AccountManagementInterface $subject,
        CustomerInterface          $customer,
        $password = null,
        $redirectUrl = ''
    ) {
        $source = $this->customerSession->getRegistrationSource();
        $email = $this->customerSession->getEmail();
        $customerEmail = $customer->getEmail();
        $status = ($customerEmail == $email);
        if ($source && $status) {
            $customer->setCustomAttribute(SessionSourceKey::REGISTRATION_SOURCE_KEY, $source);
        }
    }
}
