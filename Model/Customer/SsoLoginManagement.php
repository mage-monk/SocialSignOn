<?php

declare(strict_types=1);

namespace MageMonk\SocialSignOn\Model\Customer;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use MageMonk\SocialSignOn\Api\Customer\SsoLoginManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * class Sso login
 */
class SsoLoginManagement implements SsoLoginManagementInterface
{
    private const SECTION_DATA_COOKIE = 'section_data_clean';

    /**
     * Initialization
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerSession $customerSession
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param ValidateCustomer $validateCustomer
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct (
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CustomerSession $customerSession,
        private readonly CookieMetadataFactory $cookieMetadataFactory,
        private readonly ValidateCustomer $validateCustomer,
        private readonly CookieManagerInterface $cookieManager
    ) {
    }

    /**
     * Login by id
     *
     * @param $email
     * @return bool
     * @throws LocalizedException|UserLockedException|NoSuchEntityException
     */
    public function login($email): bool
    {
        $customer = $this->customerRepository->get($email);
        $this->validateCustomer->validate($customer);
        $loginStatus = $this->customerSession->loginById($customer->getId());
        $this->invalidateCookie();
        return $loginStatus;
    }

    /**
     * @return void
     * @throws InputException|CookieSizeLimitReachedException|FailureToSendException
     */
    private function invalidateCookie(): void
    {
        $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setHttpOnly(false)
            ->setDuration(15)
            ->setPath('/');
        try {
            $this->cookieManager->setPublicCookie(self::SECTION_DATA_COOKIE, 'true', $cookieMetadata);
        } catch (InputException $e) {
            throw new InputException($e);
        } catch (CookieSizeLimitReachedException $e) {
            throw new CookieSizeLimitReachedException($e);
        } catch (FailureToSendException $e) {
            throw new FailureToSendException($e);
        }
    }
}
